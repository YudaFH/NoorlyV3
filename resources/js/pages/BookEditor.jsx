import React, { useEffect } from 'react';
import NavbarTop from '../components/navbartop.jsx';
import SidebarTools from '../components/SidebarTools.jsx';
import EditorCanvas from '../components/editorcanvas.jsx';
import { useEditorStore } from '../hooks/useEditorStore';
import { AnimatePresence, motion } from 'framer-motion';
import Toasts from '../components/Toasts.jsx';
import TextFormatBar from '../components/TextFormatBar.jsx';

// Placeholder Turn.js integration (only active in preview mode)
// In a real setup, you would import Turn.js and initialize it on a container with pages
// Example: $("#flipbook").turn({ width: 800, height: 600 });

export default function BookEditor() {
  const {
    mode,
    setMode,
    isPreview,
    togglePreview,
    sections,
    setSections,
    addElement,
    undo,
    redo,
    saveLayout,
  } = useEditorStore();
  const [selectedSectionId, setSelectedSectionId] = React.useState(null);
  const [selectedId, setSelectedId] = React.useState(null);
  const [rightPanel, setRightPanel] = React.useState(null); // 'text-advanced' | 'text-effects' | 'text-animate' | 'position'
  const [toasts, setToasts] = React.useState([]);

  const addToast = (msg) => {
    const id = Date.now().toString();
    setToasts((prev) => [...prev, { id, msg }]);
    setTimeout(() => setToasts((prev) => prev.filter((t) => t.id !== id)), 2000);
  };

  const currentSection = React.useMemo(() => {
    if (!sections || sections.length === 0) return null;
    return sections.find((s) => s.id === selectedSectionId) || sections[0];
  }, [sections, selectedSectionId]);

  const setElementsInCurrentSection = (updater) => {
    if (!currentSection) return;
    setSections((prev) => {
      const idx = prev.findIndex((s) => s.id === currentSection.id);
      if (idx < 0) return prev;
      const next = [...prev];
      const newElements = typeof updater === 'function' ? updater(next[idx].elements) : updater;
      next[idx] = { ...next[idx], elements: newElements };
      return next;
    });
  };

  const moveLayer = (dir) => {
    if (!selectedId || !currentSection) return;
    setElementsInCurrentSection((prev) => {
      const idx = prev.findIndex((e) => e.id === selectedId);
      if (idx < 0) return prev;
      const arr = [...prev];
      const [item] = arr.splice(idx, 1);
      const target = dir === 'forward' ? Math.min(idx + 1, arr.length) : Math.max(idx - 1, 0);
      arr.splice(target, 0, item);
      return arr;
    });
    addToast(dir === 'forward' ? 'Layer moved forward' : 'Layer moved backward');
  };

  const handleAdd = (type) => {
    if (!currentSection) return;
    const id = Date.now().toString();
    if (type === 'background') {
      setSections((prev) => prev.map((s) => (s.id === currentSection.id ? { ...s, bgColor: '#FFC72C' } : s)));
      return;
    }
    if (type === 'text') {
      addElement(currentSection.id, { id, type: 'text', text: 'Teks baru', x: 60, y: 60, width: 220, height: 50, fontSize: 28, fill: '#111827' });
      setSelectedId(id);
    } else if (type === 'image') {
      addElement(currentSection.id, { id, type: 'image', src: 'https://via.placeholder.com/400x250.png?text=Image', x: 120, y: 100, width: 400, height: 250 });
      setSelectedId(id);
    } else if (type === 'shape') {
      addElement(currentSection.id, { id, type: 'rect', x: 140, y: 140, width: 180, height: 130, fill: '#FFC72C', cornerRadius: 16 });
      setSelectedId(id);
    } else if (type === 'video') {
      addElement(currentSection.id, { id, type: 'text', text: '[Video Placeholder]', x: 80, y: 80, width: 280, height: 60, fontSize: 20, fill: '#dc2626' });
    } else if (type === 'audio') {
      addElement(currentSection.id, { id, type: 'text', text: '[Audio Placeholder]', x: 90, y: 90, width: 280, height: 60, fontSize: 20, fill: '#2563eb' });
    }
  };

  const handleSave = async () => {
    const res = await saveLayout();
    if (res.ok) {
      alert('Layout saved!');
    } else {
      alert('Failed to save: ' + res.error);
    }
  };

  const addTextPreset = (preset) => {
    const id = Date.now().toString();
    addElement(currentSection.id, { id, type: 'text', text: preset.text || 'Text', x: 80, y: 80, width: 300, height: 60, fontSize: preset.fontSize || 24, fill: '#111827' });
    addToast('Text added');
    setSelectedId(id);
  };

  const addShapeKind = (kind) => {
    const id = Date.now().toString();
    const base = { id, x: 120, y: 120, rotation: 0 };
    if (kind === 'rectangle') {
      addElement(currentSection.id, { ...base, type: 'rect', width: 180, height: 120, fill: '#FFC72C' });
    } else if (kind === 'circle') {
      addElement(currentSection.id, { ...base, type: 'circle', radius: 80, fill: '#FFC72C' });
    } else if (kind === 'triangle') {
      addElement(currentSection.id, { ...base, type: 'triangle', width: 160, height: 140, fill: '#FFC72C' });
    } else if (kind === 'pentagon' || kind === 'hexagon' || kind === 'star') {
      addElement(currentSection.id, { ...base, type: 'polygon', sides: kind === 'pentagon' ? 5 : kind === 'hexagon' ? 6 : 5, innerRadius: 40, outerRadius: 80, fill: '#FFC72C', isStar: kind === 'star' });
    } else if (kind === 'line') {
      addElement(currentSection.id, { ...base, type: 'line', points: [0, 0, 150, 0], stroke: '#111827', strokeWidth: 4 });
    } else if (kind === 'arrow') {
      addElement(currentSection.id, { ...base, type: 'arrow', points: [0, 0, 150, 0], stroke: '#111827', strokeWidth: 4 });
    }
    addToast('Element added');
    setSelectedId(id);
  };

  const onBackground = (color) => {
    if (!currentSection) return;
    setSections((prev) => prev.map((s) => (s.id === currentSection.id ? { ...s, bgColor: color } : s)));
  };

  const onUploadsAction = (action) => {
    // Placeholder: integrate real upload modal later
    alert('Uploads action: ' + action);
  };

  return (
    <div className="h-screen flex flex-col bg-gray-100">
      <NavbarTop
        mode={mode}
        onModeChange={setMode}
        onPreview={togglePreview}
        isPreview={isPreview}
        onSave={handleSave}
      />
      <div className="flex flex-1 overflow-hidden">
        <SidebarTools
          onAddTextStyle={addTextPreset}
          onAddShape={addShapeKind}
          onBackground={onBackground}
          onUploadsAction={onUploadsAction}
          onUndo={undo}
          elements={currentSection?.elements || []}
          setElements={setElementsInCurrentSection}
          selectedId={selectedId}
          setSelectedId={setSelectedId}
        />
        <div className="flex-1 relative">
          <AnimatePresence>
            {!isPreview && (
              <motion.div
                key="editor"
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -20 }}
                className="absolute inset-0 flex flex-col"
              >
                {/* Fixed text format bar under navbar, above canvas */}
                {(() => {
                  const el = (currentSection?.elements || []).find((e) => e.id === selectedId);
                  if (!el || el.type !== 'text') return null;
                  const update = (attrs) => setElementsInCurrentSection((prev) => prev.map((x) => (x.id === el.id ? { ...x, ...attrs } : x)));
                  return (
                    <div className="pt-3 pb-2 flex justify-center">
                      <TextFormatBar el={el} update={update} onOpenRightPanel={setRightPanel} onToast={addToast} variant="fixed" />
                    </div>
                  );
                })()}
                <EditorCanvas
                  sections={sections}
                  setSections={setSections}
                  mode={mode}
                  onUndo={undo}
                  selectedSectionIdExternal={selectedSectionId}
                  setSelectedSectionIdExternal={setSelectedSectionId}
                  selectedElementIdExternal={selectedId}
                  setSelectedElementIdExternal={setSelectedId}
                  addElementMirror={addElement}
                  onOpenRightPanel={setRightPanel}
                  onToast={addToast}
                />
              </motion.div>
            )}
            {isPreview && (
              <motion.div
                key="preview"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                className="absolute inset-0 p-6 overflow-auto"
              >
                <div className="max-w-5xl mx-auto">
                  <h2 className="text-xl font-semibold mb-4">Preview Buku</h2>
                  <div id="flipbook" className="bg-white rounded-2xl shadow-lg border p-6">
                    {/* Simple page representation; replace with real Turn.js pages */}
                    <div className="space-y-8">
                      {sections.map((sec) => (
                        <div key={sec.id} className="rounded-xl border overflow-hidden" style={{ background: sec.bgColor }}>
                          <div className="p-4">
                            {(sec.elements || []).map((el) => (
                              <div key={el.id} className="text-sm text-gray-700">
                                {el.type === 'text' && <p style={{ fontSize: el.fontSize }}>{el.text}</p>}
                                {el.type === 'image' && (
                                  <img src={el.src} alt="" className="rounded-xl shadow-sm" style={{ width: el.width }} />
                                )}
                                {el.type === 'rect' && (
                                  <div className="rounded-xl" style={{ width: el.width, height: el.height, background: el.fill }} />
                                )}
                              </div>
                            ))}
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                  <p className="text-xs text-gray-500 mt-4">Turn.js effect placeholder. Integrate real flipbook after mounting.</p>
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </div>
        {/* Right-side advanced panel */}
        <AnimatePresence initial={false}>
          {rightPanel && (
            <motion.div
              key="right-panel"
              initial={{ width: 0, opacity: 0 }}
              animate={{ width: 320, opacity: 1 }}
              exit={{ width: 0, opacity: 0 }}
              transition={{ type: 'tween', duration: 0.18 }}
              className="h-full bg-white border-l overflow-hidden"
            >
              <div className="h-full w-80 p-4 overflow-y-auto">
                <div className="flex items-center justify-between mb-4">
                  <div className="text-sm font-semibold text-gray-700">
                    {rightPanel === 'text-advanced' && 'Text Advanced'}
                    {rightPanel === 'position' && 'Position'}
                  </div>
                  <button className="text-xs px-2 py-1 rounded bg-gray-100 hover:bg-gray-200" onClick={() => setRightPanel(null)}>Tutup</button>
                </div>
                {rightPanel === 'text-advanced' && (
                  <div className="space-y-3">
                    <div>
                      <label className="text-xs text-gray-600">Letter Spacing</label>
                      <input type="range" min="-2" max="10" step="0.5" className="w-full"
                        onChange={(e) => {
                          if (!selectedId) return;
                          setElementsInCurrentSection((prev) => prev.map((el) => el.id === selectedId ? { ...el, letterSpacing: parseFloat(e.target.value) } : el));
                        }}
                      />
                    </div>
                    <div>
                      <label className="text-xs text-gray-600">Line Spacing</label>
                      <input type="range" min="0.8" max="2" step="0.05" className="w-full"
                        onChange={(e) => {
                          if (!selectedId) return;
                          setElementsInCurrentSection((prev) => prev.map((el) => el.id === selectedId ? { ...el, lineHeight: parseFloat(e.target.value) } : el));
                        }}
                      />
                    </div>
                    <div>
                      <label className="text-xs text-gray-600">Anchor Text</label>
                      <select className="w-full border rounded px-2 py-1 text-sm" onChange={(e) => {
                        if (!selectedId) return;
                        setElementsInCurrentSection((prev) => prev.map((el) => el.id === selectedId ? { ...el, align: e.target.value } : el));
                      }}>
                        <option value="left">Start</option>
                        <option value="center">Center</option>
                        <option value="right">End</option>
                        <option value="justify">Justify</option>
                      </select>
                    </div>
                    <div>
                      <label className="text-xs text-gray-600">Transparency</label>
                      <input type="range" min="0" max="1" step="0.05" className="w-full"
                        onChange={(e) => {
                          if (!selectedId) return;
                          setElementsInCurrentSection((prev) => prev.map((el) => el.id === selectedId ? { ...el, opacity: parseFloat(e.target.value) } : el));
                        }}
                      />
                    </div>
                  </div>
                )}
                {rightPanel === 'position' && (
                  <div className="space-y-2">
                    <button className="w-full px-2 py-2 rounded border text-sm hover:bg-gray-50" onClick={() => moveLayer('forward')}>Bring Forward</button>
                    <button className="w-full px-2 py-2 rounded border text-sm hover:bg-gray-50" onClick={() => moveLayer('backward')}>Send Backward</button>
                  </div>
                )}
                {rightPanel === 'text-effects' && (
                  <div className="space-y-2">
                    {['none','shadow','lift','glow','outline'].map((fx) => (
                      <button
                        key={fx}
                        className={`w-full px-2 py-2 rounded border text-sm hover:bg-gray-50 ${(currentSection?.elements||[]).find(e=>e.id===selectedId)?.effectType===fx ? 'border-[#FFC72C] bg-yellow-50' : ''}`}
                        onClick={() => {
                          if (!selectedId) return;
                          setElementsInCurrentSection((prev) => prev.map((el) => el.id === selectedId ? { ...el, effectType: fx==='none'? null : fx } : el));
                          addToast('Effect applied');
                        }}
                      >{fx === 'none' ? 'None' : fx[0].toUpperCase()+fx.slice(1)}</button>
                    ))}
                  </div>
                )}
                {rightPanel === 'text-animate' && (
                  <AnimatePanel
                    elements={currentSection?.elements || []}
                    selectedId={selectedId}
                    setElements={setElementsInCurrentSection}
                    onToast={addToast}
                  />
                )}
              </div>
            </motion.div>
          )}
        </AnimatePresence>
        <Toasts items={toasts} />
      </div>
    </div>
  );
}

function AnimatePanel({ elements, selectedId, setElements, onToast }) {
  const [type, setType] = React.useState(elements.find(e=>e.id===selectedId)?.animateType || 'fade');

  const applyType = (t) => {
    setType(t);
    if (!selectedId) return;
    setElements((prev) => prev.map((el) => el.id === selectedId ? { ...el, animateType: t } : el));
  };

  const preview = () => {
    if (!selectedId) return;
    const el = elements.find(e=>e.id===selectedId);
    if (!el || el.type !== 'text') return;
    if (type === 'typewriter') {
      const original = el.text || '';
      let i = 0;
      const step = () => {
        i++;
        setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, text: original.slice(0, i) } : x));
        if (i <= original.length) {
          requestAnimationFrame(step);
        } else {
          setTimeout(() => setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, text: original } : x)), 150);
        }
      };
      setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, text: '' } : x));
      requestAnimationFrame(step);
    } else if (type === 'fade') {
      const start = performance.now();
      const dur = 600;
      const base = el.opacity ?? 1;
      const tick = (t) => {
        const k = Math.min(1, (t - start) / dur);
        const val = k;
        setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, opacity: val } : x));
        if (k < 1) requestAnimationFrame(tick); else setElements((prev)=>prev.map((x)=>x.id===el.id?{...x, opacity: base}:x));
      };
      setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, opacity: 0 } : x));
      requestAnimationFrame(tick);
    } else if (type === 'ascend') {
      const start = performance.now();
      const dur = 600;
      const baseY = el.y || 0;
      const tick = (t) => {
        const k = Math.min(1, (t - start) / dur);
        const ease = 1 - Math.pow(1 - k, 3);
        setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, y: baseY - (1 - ease) * 20 } : x));
        if (k < 1) requestAnimationFrame(tick); else setElements((prev)=>prev.map((x)=>x.id===el.id?{...x, y: baseY}:x));
      };
      setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, y: baseY + 20 } : x));
      requestAnimationFrame(tick);
    } else if (type === 'shift') {
      const start = performance.now();
      const dur = 600;
      const baseX = el.x || 0;
      const tick = (t) => {
        const k = Math.min(1, (t - start) / dur);
        const ease = 1 - Math.pow(1 - k, 3);
        setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, x: baseX - (1 - ease) * 20 } : x));
        if (k < 1) requestAnimationFrame(tick); else setElements((prev)=>prev.map((x)=>x.id===el.id?{...x, x: baseX}:x));
      };
      setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, x: baseX + 20 } : x));
      requestAnimationFrame(tick);
    } else if (type === 'pop') {
      const start = performance.now();
      const dur = 500;
      const baseSX = el.scaleX ?? 1;
      const baseSY = el.scaleY ?? 1;
      const tick = (t) => {
        const k = Math.min(1, (t - start) / dur);
        // keyframes: 0.0->0.8, 0.7->1.05, 1.0->1.0
        let s;
        if (k < 0.7) s = 0.8 + k / 0.7 * (1.05 - 0.8); else s = 1.05 + (k - 0.7) / 0.3 * (1 - 1.05);
        setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, scaleX: s, scaleY: s } : x));
        if (k < 1) requestAnimationFrame(tick); else setElements((prev)=>prev.map((x)=>x.id===el.id?{...x, scaleX: baseSX, scaleY: baseSY}:x));
      };
      setElements((prev) => prev.map((x) => x.id === el.id ? { ...x, scaleX: 0.8, scaleY: 0.8 } : x));
      requestAnimationFrame(tick);
    }
    onToast && onToast('Animation preview');
  };

  return (
    <div className="space-y-3">
      <div className="grid grid-cols-2 gap-2">
        {[
          { k:'typewriter', label:'Typewriter' },
          { k:'ascend', label:'Ascend' },
          { k:'shift', label:'Shift' },
          { k:'pop', label:'Pop' },
          { k:'fade', label:'Fade' },
        ].map((opt) => (
          <button key={opt.k}
            className={`px-2 py-2 rounded border text-sm hover:bg-gray-50 ${type===opt.k?'border-[#FFC72C] bg-yellow-50':''}`}
            onClick={() => applyType(opt.k)}
          >{opt.label}</button>
        ))}
      </div>
      <div className="pt-2">
        <button className="w-full px-2 py-2 rounded bg-[#FFC72C] text-gray-900 font-semibold hover:brightness-95" onClick={preview}>Preview</button>
      </div>
    </div>
  );
}
