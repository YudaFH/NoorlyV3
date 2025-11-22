import React from 'react';
import { Monitor, Smartphone, Eye, Save } from 'lucide-react';
import { motion } from 'framer-motion';

export default function NavbarTop({ mode = 'web', onModeChange, onPreview, onSave, isPreview }) {
  return (
    <div className="w-full bg-white border-b sticky top-0 z-40">
      <div className="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-xl bg-[#FFC72C] shadow-md" />
          <div>
            <p className="text-sm text-gray-500">Noorly Builder</p>
            <h1 className="font-semibold -mt-1">E-Book Editor</h1>
          </div>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={onPreview}
            className={`inline-flex items-center gap-2 px-3 py-2 rounded-2xl shadow-sm border text-sm transition-colors ${
              isPreview ? 'bg-gray-900 text-white' : 'bg-white hover:bg-gray-50'
            }`}
          >
            <Eye size={16}/>
            {isPreview ? 'Close Preview' : 'Preview'}
          </button>

          <motion.button
            whileTap={{ scale: 0.98 }}
            onClick={onSave}
            className="inline-flex items-center gap-2 px-3 py-2 rounded-2xl shadow-sm border bg-[#FFC72C] text-gray-900 text-sm hover:brightness-95"
          >
            <Save size={16} />
            Save
          </motion.button>

          <div className="ml-2 flex rounded-2xl overflow-hidden shadow-sm border">
            <button
              onClick={() => onModeChange?.('web')}
              className={`px-3 py-2 flex items-center gap-2 text-sm ${
                mode === 'web' ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              }`}
            >
              <Monitor size={16} /> Web
            </button>
            <button
              onClick={() => onModeChange?.('mobile')}
              className={`px-3 py-2 flex items-center gap-2 text-sm border-l ${
                mode === 'mobile' ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              }`}
            >
              <Smartphone size={16} /> Mobile
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
