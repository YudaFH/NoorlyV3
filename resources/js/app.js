import './bootstrap';
// Ensure React Fast Refresh preamble is loaded in dev to avoid '@vitejs/plugin-react can't detect preamble' error
// NOTE: Manual import of 'virtual:react-refresh' removed because plugin already injects it; leaving conditional caused resolution error.

// Dynamic mount for React BookEditor
import React from 'react';
import { createRoot } from 'react-dom/client';
import BookEditor from './pages/BookEditor.jsx';

class ErrorBoundary extends React.Component {
	constructor(props) {
		super(props);
		this.state = { hasError: false, error: null };
	}
	static getDerivedStateFromError(error) {
		return { hasError: true, error };
	}
	componentDidCatch(error, errorInfo) {
		console.error('BookEditor runtime error:', error, errorInfo);
	}
	render() {
		if (this.state.hasError) {
			return React.createElement(
				'div',
				{ style: { padding: 16, color: '#b91c1c', background: '#fff1f2', border: '1px solid #fecaca', borderRadius: 8, margin: 16 } },
				'Terjadi error saat memuat Editor: ', String(this.state.error)
			);
		}
		return this.props.children;
	}
}

function mountBookEditor() {
	try {
		const target = document.getElementById('book-editor-root');
		if (!target) {
			// Not on editor page; nothing to do
			return;
		}
		if (target.__rootMounted) {
			return; // avoid double mounts
		}
		console.log('[BookEditor] Mounting...');
		const root = createRoot(target);
		target.__rootMounted = true;
		root.render(React.createElement(ErrorBoundary, null, React.createElement(BookEditor)));
		console.log('[BookEditor] Mounted ✅');
	} catch (e) {
		console.error('[BookEditor] Mount error:', e);
		const target = document.getElementById('book-editor-root');
		if (target) {
			target.innerHTML = `<pre style="padding:12px;background:#fff1f2;border:1px solid #fecaca;border-radius:8px;color:#7f1d1d;overflow:auto">${String(e && e.stack || e)}</pre>`;
		}
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', mountBookEditor);
} else {
	mountBookEditor();
}
