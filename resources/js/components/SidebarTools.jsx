import React, { useMemo, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Type, Shapes, UploadCloud, Box, Palette, Undo2, Layers as LayersIcon } from 'lucide-react';

const TOOLS = [
	{ key: 'text', label: 'Text', icon: Type, hint: 'Tambahkan judul, subjudul, atau paragraf.' },
	{ key: 'shapes', label: 'Shapes', icon: Shapes, hint: 'Bentuk dasar, poligon, bintang, garis & panah.' },
	{ key: 'uploads', label: 'Uploads', icon: UploadCloud, hint: 'Unggah gambar dan media dari perangkat kamu.' },
	{ key: 'elements', label: 'Elements', icon: Box, hint: 'Elemen siap pakai (segera hadir).' },
	{ key: 'background', label: 'Background', icon: Palette, hint: 'Ubah warna latar halaman.' },
	{ key: 'layers', label: 'Layers', icon: LayersIcon, hint: 'Kelola urutan, visibilitas, dan kunci elemen.' },
];

export default function SidebarTools({
	onAddTextStyle,
	onAddShape,
	onBackground,
	onUploadsAction,
	onUndo,
	elements,
	setElements,
	selectedId,
	setSelectedId,
}) {
	const [active, setActive] = useState(null); // which tool panel is open
	const [hoverKey, setHoverKey] = useState(null);

	const ActivePanel = useMemo(() => {
		switch (active) {
			case 'text':
				return <TextPanel onAddTextStyle={onAddTextStyle} />;
			case 'shapes':
				return <ShapesPanel onAddShape={onAddShape} />;
			case 'uploads':
				return <UploadsPanel onUploadsAction={onUploadsAction} />;
			case 'background':
				return <BackgroundPanel onBackground={onBackground} />;
			case 'elements':
				return <ElementsPanel />;
			default:
				return null;
		}
	}, [active, onAddTextStyle, onAddShape, onBackground, onUploadsAction]);

		const activeMeta = TOOLS.find((t) => t.key === active);

	return (
		<aside className="h-full flex">
			{/* Icon rail */}
					<div className="w-14 bg-white border-r h-full flex flex-col items-center py-2 relative">
				<div className="flex-1 flex flex-col gap-2">
					{TOOLS.map((t) => {
						const Icon = t.icon;
						const isActive = active === t.key;
						return (
							<button
								key={t.key}
										className={`w-10 h-10 grid place-items-center rounded-lg border transition-colors hover:bg-gray-50 ${
											isActive ? 'bg-yellow-100 border-yellow-300' : 'bg-white border-gray-200'
										}`}
								title={t.label}
										onClick={() => setActive((p) => (p === t.key ? null : t.key))}
										onMouseEnter={() => setHoverKey(t.key)}
										onMouseLeave={() => setHoverKey((k) => (k === t.key ? null : k))}
							>
										<Icon size={18} className={isActive ? 'text-[#FFC72C]' : 'text-gray-700'} />
							</button>
						);
					})}
				</div>
				<div className="mt-2 w-full px-2">
					<button
						className="w-full h-9 grid place-items-center rounded-lg border bg-white hover:bg-gray-50 border-gray-200"
						title="Undo"
						onClick={onUndo}
					>
						<Undo2 size={18} className="text-gray-700" />
					</button>
				</div>
						{/* Tooltip beside icon on hover */}
								<AnimatePresence>
							{hoverKey && (
								<motion.div
									key="icon-tooltip"
									initial={{ opacity: 0, x: 4 }}
									animate={{ opacity: 1, x: 0 }}
									exit={{ opacity: 0, x: 4 }}
											className="absolute left-14 ml-2 px-2 py-1 rounded bg-gray-900 text-white text-[10px] pointer-events-none shadow"
											style={{ top: 48 + TOOLS.findIndex(t=>t.key===hoverKey)*44 }}
								>
									{TOOLS.find(t=>t.key===hoverKey)?.label}
								</motion.div>
							)}
						</AnimatePresence>

						{/* Explanation area */}
				<div className="w-full px-2 pt-2 text-[10px] text-gray-500">
					{activeMeta ? (
						<div>
							<div className="font-medium text-gray-700">{activeMeta.label}</div>
							<div className="opacity-80 leading-snug mt-0.5">{activeMeta.hint}</div>
						</div>
					) : (
						<div className="opacity-70">Pilih tool</div>
					)}
				</div>
			</div>

			{/* Open side panel */}
			<AnimatePresence initial={false}>
				{active && (
					<motion.div
						key="open-panel"
						initial={{ width: 0, opacity: 0 }}
						animate={{ width: 288, opacity: 1 }}
						exit={{ width: 0, opacity: 0 }}
						transition={{ type: 'tween', duration: 0.18 }}
						className="h-full bg-white border-r overflow-hidden"
					>
						<div className="h-full w-72 p-3 overflow-y-auto">
							<div className="flex items-center justify-between mb-3">
								<div className="text-sm font-semibold text-gray-700">{activeMeta?.label}</div>
								<button
									className="text-xs px-2 py-1 rounded bg-gray-100 hover:bg-gray-200"
									onClick={() => setActive(null)}
								>
									Tutup
								</button>
							</div>
									{active === 'layers' ? (
										<LayersPanel
											elements={elements}
											setElements={setElements}
											selectedId={selectedId}
											setSelectedId={setSelectedId}
										/>
									) : (
										ActivePanel
									)}
						</div>
					</motion.div>
				)}
			</AnimatePresence>
		</aside>
	);
}

function TextPanel({ onAddTextStyle }) {
	const STYLES = [
		{ key: 'add', label: 'Add a Text Box', preset: { fontSize: 28, text: 'New Text' }, primary: true },
		{ key: 'heading', label: 'Add a heading', preset: { fontSize: 48, text: 'Add a heading' }, displaySize: 'text-2xl font-bold' },
		{ key: 'subheading', label: 'Add a subheading', preset: { fontSize: 28, text: 'Add a subheading' }, displaySize: 'text-lg font-semibold' },
		{ key: 'body', label: 'Add a little bit of body text', preset: { fontSize: 16, text: 'Add a little bit of body text' }, displaySize: 'text-sm text-gray-600' },
	];
	return (
		<div className="space-y-3">
			{STYLES.map((s) => (
				<button
					key={s.key}
					onClick={() => onAddTextStyle?.(s.preset)}
					className={
						s.primary
							? 'block w-full text-left px-4 py-3 rounded-xl border shadow-sm bg-[#FFC72C] hover:brightness-95 text-gray-900 font-semibold'
							: 'block w-full text-left px-4 py-3 rounded-xl border bg-white hover:bg-gray-50'
					}
				>
					<div className={s.displaySize || 'text-sm'}>{s.label}</div>
				</button>
			))}
			<div className="mt-2">
				<p className="text-[11px] font-semibold text-gray-500">Default Text Styles</p>
			</div>
		</div>
	);
}

function ShapesPanel({ onAddShape }) {
	const CATEGORIES = [
		{ name: 'Basic Shapes', items: ['Rectangle', 'Circle', 'Triangle'] },
		{ name: 'Polygons', items: ['Pentagon', 'Hexagon'] },
		{ name: 'Stars', items: ['Star'] },
		{ name: 'Lines and Arrows', items: ['Line', 'Arrow'] },
	];
	return (
		<div className="space-y-3">
			{CATEGORIES.map((c) => (
				<div key={c.name} className="">
					<p className="text-[11px] font-semibold text-gray-500 mb-1">{c.name}</p>
					<div className="grid gap-1">
						{c.items.map((shape) => (
							<button
								key={shape}
								onClick={() => onAddShape?.(shape.toLowerCase())}
								className="text-[11px] text-left px-2 py-1 rounded bg-gray-50 hover:bg-gray-100 border"
							>
								{shape}
							</button>
						))}
					</div>
				</div>
			))}
		</div>
	);
}

function UploadsPanel({ onUploadsAction }) {
	const ACTIONS = ['Upload Files', 'Recent Uploads', 'Media Library'];
	return (
		<div className="space-y-2">
			{ACTIONS.map((a) => (
				<button
					key={a}
						onClick={() => onUploadsAction?.(a)}
					className="block w-full text-left text-xs px-3 py-2 rounded border bg-white hover:bg-gray-50"
				>
					{a}
				</button>
			))}
		</div>
	);
}

function BackgroundPanel({ onBackground }) {
	const COLORS = ['#ffffff', '#f8fafc', '#FFC72C', '#1e293b', '#0f172a'];
	return (
		<div className="space-y-2">
			<p className="text-[11px] font-semibold text-gray-500">Background Colors</p>
			<div className="flex flex-wrap gap-1">
				{COLORS.map((c) => (
					<button
						key={c}
						onClick={() => onBackground?.(c)}
						className="w-8 h-8 rounded border shadow-sm"
						style={{ background: c }}
						title={c}
					/>
				))}
			</div>
		</div>
	);
}

function ElementsPanel() {
	return (
		<div className="text-[11px] text-gray-500">No elements configured yet.</div>
	);
}

function LayersPanel({ elements = [], setElements, selectedId, setSelectedId }) {
	const [editingId, setEditingId] = React.useState(null);
	const [nameDraft, setNameDraft] = React.useState('');

	const startEdit = (el) => {
		setEditingId(el.id);
		setNameDraft(el.name || `${el.type}`);
	};
	const commitEdit = (id) => {
		setElements((prev) => prev.map((el) => (el.id === id ? { ...el, name: nameDraft } : el)));
		setEditingId(null);
	};

	const onDragStart = (e, index) => {
		e.dataTransfer.setData('text/plain', String(index));
	};
	const onDrop = (e, overIndex) => {
		const from = parseInt(e.dataTransfer.getData('text/plain'), 10);
		if (isNaN(from)) return;
		setElements((prev) => {
			const arr = [...prev];
			const [moved] = arr.splice(from, 1);
			arr.splice(overIndex, 0, moved);
			return arr;
		});
	};

	return (
		<div className="space-y-1">
			{elements.map((el, idx) => (
				<div
					key={el.id}
					className={`flex items-center gap-2 px-2 py-1 rounded border ${selectedId === el.id ? 'bg-yellow-50 border-yellow-200' : 'bg-white border-gray-200'}`}
					draggable
					onDragStart={(e) => onDragStart(e, idx)}
					onDragOver={(e) => e.preventDefault()}
					onDrop={(e) => onDrop(e, idx)}
				>
					<button
						className={`w-2 h-2 rounded-full ${el.hidden ? 'bg-gray-300' : 'bg-[#FFC72C]'}`}
						title={el.hidden ? 'Show' : 'Hide'}
						onClick={() => setElements((prev) => prev.map((x) => (x.id === el.id ? { ...x, hidden: !x.hidden } : x)))}
					/>
					<button
						className={`w-2 h-2 rounded ${el.locked ? 'bg-gray-700' : 'bg-gray-300'}`}
						title={el.locked ? 'Unlock' : 'Lock'}
						onClick={() => setElements((prev) => prev.map((x) => (x.id === el.id ? { ...x, locked: !x.locked } : x)))}
					/>
					<div className="flex-1" onClick={() => setSelectedId?.(el.id)}>
						{editingId === el.id ? (
							<input
								value={nameDraft}
								onChange={(e) => setNameDraft(e.target.value)}
								onBlur={() => commitEdit(el.id)}
								onKeyDown={(e) => {
									if (e.key === 'Enter') commitEdit(el.id);
									if (e.key === 'Escape') setEditingId(null);
								}}
								className="w-full text-xs px-1 py-0.5 border rounded"
								autoFocus
							/>
						) : (
							<div className="text-xs cursor-text" onDoubleClick={() => startEdit(el)}>
								{el.name || `${el.type}`}
							</div>
						)}
					</div>
				</div>
			))}
			<p className="text-[10px] text-gray-500">Drag untuk reorder. Klik bulatan untuk show/hide, kotak untuk lock/unlock. Double click nama untuk rename.</p>
		</div>
	);
}
