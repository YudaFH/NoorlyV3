import React from 'react';
import { AnimatePresence, motion } from 'framer-motion';

export default function Toasts({ items = [] }) {
  return (
    <div className="pointer-events-none fixed left-1/2 -translate-x-1/2 bottom-6 z-50 flex flex-col gap-2">
      <AnimatePresence>
        {items.map((t) => (
          <motion.div
            key={t.id}
            initial={{ opacity: 0, y: 8 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 8 }}
            className="px-3 py-2 rounded-lg bg-gray-900 text-white text-xs shadow-lg"
          >
            {t.msg}
          </motion.div>
        ))}
      </AnimatePresence>
    </div>
  );
}
