import React from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { Bold, Italic, Underline, CaseUpper, CaseLower, AlignLeft, AlignCenter, AlignRight, AlignJustify, Plus, Minus, Settings, Layers as LayersIcon, List, ListOrdered, Wand2, PlayCircle, Palette } from 'lucide-react';

export default function TextFormatBar({
  x,
  y,
  el,
  update,
  onOpenRightPanel,
  onToast,
  variant = 'floating', // 'floating' | 'fixed'
}) {
  const fontFamilies = ['Inter', 'Arial', 'Georgia', 'Times New Roman'];
  const colors = ['#111827', '#FFC72C', '#dc2626', '#2563eb', '#10b981'];
  const [showColors, setShowColors] = React.useState(false);

  const toggleBold = () => {
    const curr = (el.fontStyle || '').toLowerCase();
    const parts = new Set(curr.split(' ').filter(Boolean));
    if (parts.has('bold')) parts.delete('bold'); else parts.add('bold');
    update({ fontStyle: Array.from(parts).join(' ') || 'normal' });
  };
  const toggleItalic = () => {
    const curr = (el.fontStyle || '').toLowerCase();
    const parts = new Set(curr.split(' ').filter(Boolean));
    if (parts.has('italic')) parts.delete('italic'); else parts.add('italic');
    update({ fontStyle: Array.from(parts).join(' ') || 'normal' });
  };
  const toggleUnderline = () => {
    const curr = (el.textDecoration || '').toLowerCase();
    const parts = new Set(curr.split(' ').filter(Boolean));
    if (parts.has('underline')) parts.delete('underline'); else parts.add('underline');
    update({ textDecoration: Array.from(parts).join(' ') || '' });
  };
  const incFont = () => update({ fontSize: Math.min((el.fontSize || 24) + 2, 200) });
  const decFont = () => update({ fontSize: Math.max((el.fontSize || 24) - 2, 8) });
  const setAlign = (align) => update({ align });
  const toUpper = () => update({ text: (el.text || '').toUpperCase() });
  const toLower = () => update({ text: (el.text || '').toLowerCase() });

  const stripListPrefixes = (text) => {
    return (text || '').split(/\r?\n/).map((line) => line.replace(/^\s*(\u2022\s+|\d+\.\s+)/, '')).join('\n');
  };
  const setBulleted = () => {
    const isBullet = el.listType === 'bullet';
    if (isBullet) {
      update({ listType: null, text: stripListPrefixes(el.text || '') });
      onToast && onToast('Bulleted list removed');
    } else {
      const lines = (el.text || 'List item').split(/\r?\n/);
      const out = lines.map((l) => `\u2022 ${l.replace(/^\s*(\u2022\s+|\d+\.\s+)/, '')}`);
      update({ listType: 'bullet', text: out.join('\n') });
      onToast && onToast('Bulleted list applied');
    }
  };
  const setNumbered = () => {
    const isNumbered = el.listType === 'numbered';
    if (isNumbered) {
      update({ listType: null, text: stripListPrefixes(el.text || '') });
      onToast && onToast('Numbered list removed');
    } else {
      const lines = (el.text || 'List item').split(/\r?\n/);
      const out = lines.map((l, i) => `${i + 1}. ${l.replace(/^\s*(\u2022\s+|\d+\.\s+)/, '')}`);
      update({ listType: 'numbered', text: out.join('\n') });
      onToast && onToast('Numbered list applied');
    }
  };

  return (
    <AnimatePresence>
      <motion.div
        initial={{ opacity: 0, y: 6 }}
        animate={{ opacity: 1, y: 0 }}
        exit={{ opacity: 0, y: 6 }}
        transition={{ duration: 0.15 }}
        className={variant === 'fixed' ? 'relative z-10' : 'absolute z-30'}
        style={variant === 'fixed' ? undefined : { left: x, top: y }}
      >
  <div className="relative flex items-center gap-1 rounded-lg bg-white text-gray-800 text-xs px-1.5 py-1 shadow-lg border">
          {/* Font family */}
          <select
            className="text-xs px-1 py-1 rounded border bg-white"
            value={el.fontFamily || ''}
            onChange={(e) => update({ fontFamily: e.target.value })}
            title="Font Family"
          >
            <option value="">Default</option>
            {fontFamilies.map((f) => (
              <option key={f} value={f}>{f}</option>
            ))}
          </select>

          {/* Font size */}
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Decrease size" onClick={decFont}><Minus size={14} /></button>
          <div className="px-1 min-w-7 text-center">{el.fontSize || 24}</div>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Increase size" onClick={incFont}><Plus size={14} /></button>

          {/* Color palette (toggle) */}
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Text color" onClick={() => setShowColors((s) => !s)}>
            <Palette size={14} />
          </button>
          <AnimatePresence>
            {showColors && (
              <motion.div
                initial={{ opacity: 0, y: 4 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: 4 }}
                transition={{ duration: 0.15 }}
                className="absolute left-1/2 -translate-x-1/2 top-full mt-2 bg-white border rounded-md shadow-md p-2 flex gap-1 z-40"
              >
                {colors.map((c) => (
                  <button
                    key={c}
                    className="w-5 h-5 rounded border"
                    style={{ background: c }}
                    onClick={() => {
                      update({ fill: c });
                      setShowColors(false);
                    }}
                    title={c}
                  />
                ))}
              </motion.div>
            )}
          </AnimatePresence>

          <div className="mx-1 w-px h-5 bg-gray-200" />

          {/* B I U */}
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Bold" onClick={toggleBold}><Bold size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Italic" onClick={toggleItalic}><Italic size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Underline" onClick={toggleUnderline}><Underline size={14} /></button>

          <div className="mx-1 w-px h-5 bg-gray-200" />

          {/* Case */}
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Uppercase" onClick={toUpper}><CaseUpper size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Lowercase" onClick={toLower}><CaseLower size={14} /></button>

          <div className="mx-1 w-px h-5 bg-gray-200" />

          {/* Align */}
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Align Left" onClick={() => setAlign('left')}><AlignLeft size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Align Center" onClick={() => setAlign('center')}><AlignCenter size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Align Right" onClick={() => setAlign('right')}><AlignRight size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Justify" onClick={() => setAlign('justify')}><AlignJustify size={14} /></button>

          <div className="mx-1 w-px h-5 bg-gray-200" />

          {/* Lists */}
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Bulleted List" onClick={setBulleted}><List size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Numbered List" onClick={setNumbered}><ListOrdered size={14} /></button>

          <div className="mx-1 w-px h-5 bg-gray-200" />

          {/* Advanced & Position */}
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Advanced Settings" onClick={() => onOpenRightPanel?.('text-advanced')}><Settings size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Position" onClick={() => onOpenRightPanel?.('position')}><LayersIcon size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Effects" onClick={() => onOpenRightPanel?.('text-effects')}><Wand2 size={14} /></button>
          <button className="px-1.5 py-1 rounded hover:bg-gray-100" title="Animate" onClick={() => onOpenRightPanel?.('text-animate')}><PlayCircle size={14} /></button>
        </div>
      </motion.div>
    </AnimatePresence>
  );
}
