import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';

export default function Snackbar({ visible, x, y, onEdit, onDuplicate, onDelete, onUndo }) {
  return (
    <AnimatePresence>
      {visible && (
        <motion.div
          initial={{ opacity: 0, y: 6 }}
          animate={{ opacity: 1, y: 0 }}
          exit={{ opacity: 0, y: 6 }}
          transition={{ duration: 0.15 }}
          className="absolute z-20"
          style={{ left: x, top: y }}
        >
          <div className="flex items-center gap-1 rounded-lg bg-gray-900/90 text-white text-xs px-2 py-1 shadow-lg border border-black/20">
            <button className="px-2 py-1 hover:bg-white/10 rounded" onClick={onEdit}>Edit</button>
            <span className="opacity-30">|</span>
            <button className="px-2 py-1 hover:bg-white/10 rounded" onClick={onDuplicate}>Duplicate</button>
            <span className="opacity-30">|</span>
            <button className="px-2 py-1 hover:bg-white/10 rounded" onClick={onDelete}>Delete</button>
            <span className="opacity-30">|</span>
            <button className="px-2 py-1 hover:bg-white/10 rounded" onClick={onUndo}>Undo</button>
          </div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}
