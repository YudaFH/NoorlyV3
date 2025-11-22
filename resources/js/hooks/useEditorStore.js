import { useCallback, useMemo, useState } from 'react';
import axios from 'axios';

// Section shape: { id, height, bgColor, elements: Element[] }
// Element shape: { id, type, x,y,width,height, ...props }

export function useEditorStore() {
  const [mode, setMode] = useState('web'); // 'web' | 'mobile'
  const [isPreview, setIsPreview] = useState(false);

  // Separate layouts for web and mobile
  const [webSections, setWebSections] = useState([defaultSection()]);
  const [mobileSections, setMobileSections] = useState([defaultSection({ height: 680 })]);
  // When false, mobile layout follows web changes automatically until mobile is edited.
  const [mobileDetached, setMobileDetached] = useState(false);

  // History/redo per mode
  const [historyWeb, setHistoryWeb] = useState([]);
  const [redoWeb, setRedoWeb] = useState([]);
  const [historyMobile, setHistoryMobile] = useState([]);
  const [redoMobile, setRedoMobile] = useState([]);

  const togglePreview = useCallback(() => setIsPreview((p) => !p), []);

  const currentSections = mode === 'web' ? webSections : mobileSections;

  const setSections = useCallback((next) => {
    if (mode === 'web') {
      setHistoryWeb((h) => [...h.slice(-49), webSections]);
      setRedoWeb([]);
      setWebSections((prev) => {
        const computed = typeof next === 'function' ? next(prev) : next;
        // One-way sync: propagate to mobile until it is detached by user edits in mobile mode
        if (!mobileDetached) {
          // deep clone to avoid shared references
          const clone = JSON.parse(JSON.stringify(computed));
          setMobileSections(clone);
        }
        return computed;
      });
    } else {
      setHistoryMobile((h) => [...h.slice(-49), mobileSections]);
      setRedoMobile([]);
      setMobileSections((prev) => {
        const computed = typeof next === 'function' ? next(prev) : next;
        // First edit in mobile detaches it from web sync
        setMobileDetached(true);
        return computed;
      });
    }
  }, [mode, webSections, mobileSections, mobileDetached]);

  const undo = useCallback(() => {
    if (mode === 'web') {
      setHistoryWeb((h) => {
        if (h.length === 0) return h;
        const prev = h[h.length - 1];
        setRedoWeb((r) => [...r, webSections]);
        setWebSections(prev);
        return h.slice(0, -1);
      });
    } else {
      setHistoryMobile((h) => {
        if (h.length === 0) return h;
        const prev = h[h.length - 1];
        setRedoMobile((r) => [...r, mobileSections]);
        setMobileSections(prev);
        return h.slice(0, -1);
      });
    }
  }, [mode, webSections, mobileSections]);

  const redo = useCallback(() => {
    if (mode === 'web') {
      setRedoWeb((r) => {
        if (r.length === 0) return r;
        const next = r[r.length - 1];
        setHistoryWeb((h) => [...h, webSections]);
        setWebSections(next);
        return r.slice(0, -1);
      });
    } else {
      setRedoMobile((r) => {
        if (r.length === 0) return r;
        const next = r[r.length - 1];
        setHistoryMobile((h) => [...h, mobileSections]);
        setMobileSections(next);
        return r.slice(0, -1);
      });
    }
  }, [mode, webSections, mobileSections]);

  const saveLayout = useCallback(async () => {
    const payload = {
      mode,
      web: webSections,
      mobile: mobileSections,
      updated_at: new Date().toISOString(),
    };
    try {
      const { data } = await axios.post('/api/page-layouts', payload);
      return { ok: true, data };
    } catch (err) {
      console.error(err);
      return { ok: false, error: err?.response?.data || err.message };
    }
  }, [mode, webSections, mobileSections]);

  const scaleElementForMobile = useCallback((el) => {
    const s = 2 / 3; // example: 48 -> 32
    const clone = JSON.parse(JSON.stringify(el));
    if (typeof clone.x === 'number') clone.x = Math.round(clone.x * s);
    if (typeof clone.y === 'number') clone.y = Math.round(clone.y * s);
    if (typeof clone.width === 'number') clone.width = Math.round(clone.width * s);
    if (typeof clone.height === 'number') clone.height = Math.round(clone.height * s);
    if (typeof clone.radius === 'number') clone.radius = Math.round(clone.radius * s);
    if (typeof clone.outerRadius === 'number') clone.outerRadius = Math.round(clone.outerRadius * s);
    if (typeof clone.innerRadius === 'number') clone.innerRadius = Math.round(clone.innerRadius * s);
    if (typeof clone.fontSize === 'number') clone.fontSize = Math.round(clone.fontSize * s);
    if (Array.isArray(clone.points)) clone.points = clone.points.map((n) => Math.round(n * s));
    return clone;
  }, []);

  const addElement = useCallback((sectionId, element) => {
    if (mode === 'web') {
      // add to web
      setHistoryWeb((h) => [...h.slice(-49), webSections]);
      setRedoWeb([]);
      setWebSections((prev) => prev.map((s) => (s.id === sectionId ? { ...s, elements: [...(s.elements || []), element] } : s)));
      // mirror to mobile (do not detach mobile)
      const webIndex = webSections.findIndex((s) => s.id === sectionId);
      if (webIndex >= 0) {
        const mEl = scaleElementForMobile(element);
        setMobileSections((prev) => {
          const arr = [...prev];
          // ensure section exists
          while (arr.length <= webIndex) arr.push(defaultSection({ height: 680 }));
          arr[webIndex] = { ...arr[webIndex], elements: [...(arr[webIndex].elements || []), mEl] };
          return arr;
        });
      }
    } else {
      // add to mobile only
      setHistoryMobile((h) => [...h.slice(-49), mobileSections]);
      setRedoMobile([]);
      setMobileDetached(true);
      setMobileSections((prev) => prev.map((s) => (s.id === sectionId ? { ...s, elements: [...(s.elements || []), element] } : s)));
    }
  }, [mode, webSections, mobileSections, scaleElementForMobile]);

  return useMemo(() => ({
    mode,
    setMode,
    isPreview,
    togglePreview,
    // layouts
    sections: currentSections,
    setSections,
    webSections,
    mobileSections,
    mobileDetached,
    addElement,
    // history
    undo,
    redo,
    saveLayout,
  }), [mode, isPreview, currentSections, setSections, webSections, mobileSections, mobileDetached, addElement, undo, redo, saveLayout, togglePreview]);
}

function defaultSection({ height = 760, bgColor = '#ffffff' } = {}) {
  return { id: String(Date.now() + Math.random()), height, bgColor, elements: [] };
}
