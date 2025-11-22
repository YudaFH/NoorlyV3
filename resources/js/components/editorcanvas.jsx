import React, { useEffect, useRef, useState, useMemo } from 'react';
import { Stage, Layer, Rect, Text, Image as KImage, Transformer, Circle, Line, RegularPolygon, Star, Arrow } from 'react-konva';
import useImage from 'use-image';
// import { motion } from 'framer-motion';
import Snackbar from './Snackbar.jsx';
// TextFormatBar moved to fixed position in BookEditor

function URLImage({ shapeProps, isSelected, onSelect, onChange, onDragStart, onDragEnd }) {
  const [image] = useImage(shapeProps.src);
  const shapeRef = useRef();
  const trRef = useRef();

  useEffect(() => {
    if (isSelected) {
      // attach transformer
      trRef.current.nodes([shapeRef.current]);
      trRef.current.getLayer().batchDraw();
    }
  }, [isSelected]);

  return (
    <>
      <KImage
        onClick={onSelect}
        ref={shapeRef}
        image={image}
        {...shapeProps}
        draggable
        onDragStart={onDragStart}
        onDragEnd={(e) => {
          onChange({ ...shapeProps, x: e.target.x(), y: e.target.y() });
          onDragEnd && onDragEnd(e);
        }}
        onTransformEnd={() => {
          const node = shapeRef.current;
          const scaleX = node.scaleX();
          const scaleY = node.scaleY();

          node.scaleX(1);
          node.scaleY(1);
          onChange({
            ...shapeProps,
            x: node.x(),
            y: node.y(),
            width: Math.max(5, node.width() * scaleX),
            height: Math.max(5, node.height() * scaleY),
            rotation: node.rotation(),
          });
        }}
      />
      {isSelected && (
        <Transformer ref={trRef} rotateEnabled resizeEnabled />
      )}
    </>
  );
}

function EditableText({ shapeProps, isSelected, onSelect, onChange, onStartEdit, hidden, onDragStart, onDragEnd }) {
  const shapeRef = useRef();
  const trRef = useRef();

  useEffect(() => {
    if (isSelected) {
      trRef.current.nodes([shapeRef.current]);
      trRef.current.getLayer().batchDraw();
    }
  }, [isSelected]);

  return (
    <>
      <Text
        onClick={onSelect}
        ref={shapeRef}
        {...shapeProps}
        visible={!hidden}
        textDecoration={shapeProps.textDecoration}
        draggable
        onDblClick={() => {
          try {
            const node = shapeRef.current;
            const abs = node.getAbsolutePosition();
            onStartEdit && onStartEdit({
              id: shapeProps.id,
              x: abs.x,
              y: abs.y,
              width: node.width(),
              fontSize: shapeProps.fontSize || 24,
              fontFamily: shapeProps.fontFamily || 'Arial, sans-serif',
              value: shapeProps.text || '',
            });
          } catch (e) {
            // no-op
          }
        }}
  onDragStart={onDragStart}
  onDragEnd={(e) => { onChange({ ...shapeProps, x: e.target.x(), y: e.target.y() }); onDragEnd && onDragEnd(e); }}
        onTransformEnd={() => {
          const node = shapeRef.current;
          const scaleX = node.scaleX();
          const scaleY = node.scaleY();
          node.scaleX(1);
          node.scaleY(1);
          onChange({
            ...shapeProps,
            x: node.x(),
            y: node.y(),
            width: Math.max(5, node.width() * scaleX),
            height: Math.max(5, node.height() * scaleY),
            rotation: node.rotation(),
          });
        }}
      />
      {isSelected && !shapeProps.locked && <Transformer ref={trRef} rotateEnabled resizeEnabled />}
    </>
  );
}

function EditableRect({ shapeProps, isSelected, onSelect, onChange, onDragStart, onDragEnd }) {
  const shapeRef = useRef();
  const trRef = useRef();

  useEffect(() => {
    if (isSelected) {
      trRef.current.nodes([shapeRef.current]);
      trRef.current.getLayer().batchDraw();
    }
  }, [isSelected]);

  return (
    <>
      <Rect
        onClick={onSelect}
        ref={shapeRef}
        {...shapeProps}
        draggable
  onDragStart={onDragStart}
  onDragEnd={(e) => { onChange({ ...shapeProps, x: e.target.x(), y: e.target.y() }); onDragEnd && onDragEnd(e); }}
        onTransformEnd={() => {
          const node = shapeRef.current;
          const scaleX = node.scaleX();
          const scaleY = node.scaleY();
          node.scaleX(1);
          node.scaleY(1);
          onChange({
            ...shapeProps,
            x: node.x(),
            y: node.y(),
            width: Math.max(5, node.width() * scaleX),
            height: Math.max(5, node.height() * scaleY),
            rotation: node.rotation(),
          });
        }}
      />
      {isSelected && !shapeProps.locked && <Transformer ref={trRef} rotateEnabled resizeEnabled />}
    </>
  );
}

export default function EditorCanvas({
  sections = [],
  setSections,
  mode,
  onUndo,
  selectedSectionIdExternal,
  setSelectedSectionIdExternal,
  selectedElementIdExternal,
  setSelectedElementIdExternal,
  addElementMirror,
  onOpenRightPanel,
  onToast,
}) {
  const [selectedSectionId, setSelectedSectionId] = useState(null);
  const [selectedId, setSelectedId] = useState(null);
  const containerRef = useRef(null);
  const widthRef = useRef(null);
  const stageWrapRefs = useRef({}); // map sectionId -> ref
  const [editing, setEditing] = useState(null); // {sectionId,id, x, y, width, fontSize, fontFamily, value}

  const [viewportWidth, setViewportWidth] = useState(900);
  const canvasPadding = 0;
  const canvasSize = useMemo(() => {
    if (mode === 'mobile') return { width: 390 };
    return { width: Math.max(320, viewportWidth - canvasPadding * 2) };
  }, [mode, viewportWidth]);
  const gridSize = 20;

  const currentSection = useMemo(() => sections.find((s) => s.id === selectedSectionId) || sections[0], [sections, selectedSectionId]);

  const addElement = (sectionId, type, x = 50, y = 50) => {
    const id = Date.now().toString();
    const elBase = type === 'text'
      ? { id, type: 'text', text: 'Teks baru', x, y, width: 200, height: 40, fontSize: 24, fill: '#111827' }
      : type === 'image'
      ? { id, type: 'image', src: 'https://via.placeholder.com/300x200.png?text=Image', x, y, width: 300, height: 200 }
      : { id, type: 'rect', x, y, width: 180, height: 120, fill: '#FFC72C', cornerRadius: 12 };
    if (addElementMirror) {
      addElementMirror(sectionId, elBase);
    } else {
      setSections((prev) => prev.map((s) => (s.id === sectionId ? { ...s, elements: [...(s.elements || []), elBase] } : s)));
    }
    setSelectedSectionId(sectionId);
    setSelectedId(id);
    setSelectedElementIdExternal && setSelectedElementIdExternal(id);
  };

  useEffect(() => {
    // simple keyboard delete for element
    const handler = (e) => {
      if ((e.key === 'Delete' || e.key === 'Backspace') && selectedId && currentSection) {
        setSections((prev) => prev.map((s) => (s.id === currentSection.id ? { ...s, elements: (s.elements || []).filter((el) => el.id !== selectedId) } : s)));
        setSelectedId(null);
        setSelectedElementIdExternal && setSelectedElementIdExternal(null);
      }
    };
    document.addEventListener('keydown', handler);
    return () => document.removeEventListener('keydown', handler);
  }, [selectedId, currentSection, setSections, setSelectedElementIdExternal]);

  // sync external selections
  useEffect(() => {
    if (selectedSectionIdExternal !== undefined && selectedSectionIdExternal !== selectedSectionId) {
      setSelectedSectionId(selectedSectionIdExternal);
    }
  }, [selectedSectionIdExternal]);
  useEffect(() => {
    if (selectedElementIdExternal !== undefined && selectedElementIdExternal !== selectedId) {
      setSelectedId(selectedElementIdExternal);
    }
  }, [selectedElementIdExternal]);

  const handleSelectSection = (sectionId) => {
    setSelectedSectionId(sectionId);
    setSelectedSectionIdExternal && setSelectedSectionIdExternal(sectionId);
    // keep element selection unless clicking empty; do nothing here
  };

  const handleSelectElement = (sectionId, id) => {
    if (selectedSectionId !== sectionId) {
      setSelectedSectionId(sectionId);
      setSelectedSectionIdExternal && setSelectedSectionIdExternal(sectionId);
    }
    setSelectedId(id);
    if (setSelectedElementIdExternal) setSelectedElementIdExternal(id);
  };

  const updateElement = (sectionId, id, attrs) => {
    setSections((prev) => prev.map((s) => (s.id === sectionId ? { ...s, elements: (s.elements || []).map((el) => (el.id === id ? { ...el, ...attrs } : el)) } : s)));
  };

  const onDrop = (e) => {
    e.preventDefault();
    const type = e.dataTransfer.getData('application/x-builder-tool');
    if (!type) return;
    const y = e.clientY;
    // find target section by bounding rect
    let targetSectionId = sections[0]?.id;
    let rel = { x: 50, y: 50 };
    for (const s of sections) {
      const ref = stageWrapRefs.current[s.id];
      if (!ref || !ref.current) continue;
      const rect = ref.current.getBoundingClientRect();
      if (y >= rect.top && y <= rect.bottom) {
        targetSectionId = s.id;
        rel = { x: Math.max(0, e.clientX - rect.left - 20), y: Math.max(0, e.clientY - rect.top - 20) };
        break;
      }
    }
    addElement(targetSectionId, type, rel.x, rel.y);
  };

  const addSectionAt = (index, position = 'after') => {
    const newSec = { id: String(Date.now() + Math.random()), height: mode === 'mobile' ? 680 : 760, bgColor: '#ffffff', elements: [] };
    setSections((prev) => {
      const arr = [...prev];
      const idx = position === 'before' ? index : index + 1;
      arr.splice(idx, 0, newSec);
      return arr;
    });
    onToast && onToast('Section added');
  };

  const [isAlignedToGrid, setIsAlignedToGrid] = useState(false);
  // section height drag state
  const [heightDrag, setHeightDrag] = useState({ active: false, sectionId: null, startY: 0, startH: 0, currentH: 0 });
  const renderGrid = (w, h) => {
    if (!isDraggingElement || isAlignedToGrid) return null;
    const margin = 24;
    const columns = mode === 'mobile' ? 4 : 12;
    const gutter = 16;
    const inner = w - margin * 2;
    const colWidth = (inner - gutter * (columns - 1)) / columns;
    const rects = [];
    for (let i = 0; i < columns; i++) {
      const x = margin + i * (colWidth + gutter);
      rects.push(<Rect key={`col-${i}`} x={x} y={0} width={colWidth} height={h} fill="rgba(107,114,128,0.18)" />); // gray columns
    }
    rects.push(<Rect key="ml" x={0} y={0} width={margin} height={h} fill="rgba(107,114,128,0.08)" />); // left margin
    rects.push(<Rect key="mr" x={w - margin} y={0} width={margin} height={h} fill="rgba(107,114,128,0.08)" />); // right margin
    return rects;
  };

  const roundToGrid = (n) => Math.round(n / gridSize) * gridSize;

  const [isDraggingElement, setIsDraggingElement] = useState(false);

  const checkAlignment = (w, elX, elW = 0) => {
    const margin = 24;
    const columns = mode === 'mobile' ? 4 : 12;
    const gutter = 16;
    const inner = w - margin * 2;
    const colWidth = (inner - gutter * (columns - 1)) / columns;
    const positions = Array.from({ length: columns }, (_, i) => margin + i * (colWidth + gutter));
    const tolerance = 4;
    const near = positions.some((p) => Math.abs(elX - p) <= tolerance);
    const right = elX + elW;
    const nearRight = positions.some((p) => Math.abs(right - (p + colWidth)) <= tolerance);
    return near || nearRight;
  };
  const adjustSectionHeight = (sectionId, delta) => {
    const minH = mode === 'mobile' ? 300 : 400;
    setSections((prev) => prev.map((s) => {
      if (s.id !== sectionId) return s;
      const nextH = Math.max(minH, (s.height || (mode === 'mobile' ? 680 : 760)) + delta);
      return { ...s, height: nextH };
    }));
    onToast && onToast(delta > 0 ? 'Tinggi bagian bertambah' : 'Tinggi bagian berkurang');
  };

  // start vertical drag to resize section height
  const startHeightDrag = (sectionId, clientY, baseHeight) => {
    const minH = mode === 'mobile' ? 300 : 400;
    const startH = baseHeight || (sections.find((s) => s.id === sectionId)?.height || (mode === 'mobile' ? 680 : 760));
    setHeightDrag({ active: true, sectionId, startY: clientY, startH, currentH: startH });
    const onMove = (ev) => {
      setHeightDrag((prev) => {
        if (!prev.active) return prev;
        const dy = ev.clientY - prev.startY;
        const nextH = Math.max(minH, prev.startH + dy);
        // live update section height
        setSections((old) => old.map((s) => (s.id === prev.sectionId ? { ...s, height: nextH } : s)));
        return { ...prev, currentH: nextH };
      });
    };
    const onUp = () => {
      window.removeEventListener('mousemove', onMove);
      window.removeEventListener('mouseup', onUp);
      setHeightDrag((p) => ({ ...p, active: false }));
      onToast && onToast('Tinggi bagian diperbarui');
    };
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onUp);
  };

  useEffect(() => {
    const el = containerRef.current;
    if (!el) return;
    const ro = new ResizeObserver((entries) => {
      const w = entries[0]?.contentRect?.width || el.clientWidth || 900;
      setViewportWidth(w);
    });
    ro.observe(el);
    // initial
    setViewportWidth(el.clientWidth || 900);
    return () => ro.disconnect();
  }, []);

  return (
    <div className="flex-1 h-full overflow-auto" ref={containerRef}
         onDragOver={(e) => e.preventDefault()} onDrop={onDrop}>
      <div className="pb-12" ref={widthRef}>
        {sections.map((section, idx) => {
            const stageRef = stageWrapRefs.current[section.id] || (stageWrapRefs.current[section.id] = React.createRef());
            const isActiveSection = selectedSectionId ? selectedSectionId === section.id : idx === 0;
            const secHeight = section.height || (mode === 'mobile' ? 680 : 760);
            return (
              <div key={section.id} className={`relative overflow-visible group`}
                   style={{ width: '100%' }}
                   onClick={() => handleSelectSection(section.id)}>
                <div className="mx-auto" style={{ width: canvasSize.width }} ref={stageRef}>
                  <Stage width={canvasSize.width} height={secHeight} className={'overflow-hidden border'}>
                  <Layer>
                    <Rect x={0} y={0} width={canvasSize.width} height={secHeight} fill={section.bgColor || '#ffffff'}
                          onDblClick={() => {
                            setSelectedId(null);
                            setSelectedElementIdExternal && setSelectedElementIdExternal(null);
                            setIsDraggingElement(false);
                            setIsAlignedToGrid(false);
                          }} />
                    {/* grid */}
                    {renderGrid(canvasSize.width, secHeight)}
                    {(section.elements || []).map((el) => {
                      if (el.hidden) return null;
                      const isSelected = selectedId === el.id;
                      const onDragSnap = (e) => {
                        const x = e.target.x();
                        const y = e.target.y();
                        setIsAlignedToGrid(checkAlignment(canvasSize.width, x, el.width || 0));
                        updateElement(section.id, el.id, { x: x, y: y });
                      };
                      if (el.type === 'text') {
                        const styled = { ...el };
                        if (el.effectType) {
                          if (el.effectType === 'shadow') {
                            styled.shadowColor = 'rgba(0,0,0,0.35)';
                            styled.shadowBlur = 6;
                            styled.shadowOffset = { x: 0, y: 3 };
                          } else if (el.effectType === 'lift') {
                            styled.shadowColor = 'rgba(0,0,0,0.4)';
                            styled.shadowBlur = 10;
                            styled.shadowOffset = { x: 0, y: 6 };
                          } else if (el.effectType === 'glow') {
                            styled.shadowColor = el.fill || '#FFC72C';
                            styled.shadowBlur = 20;
                            styled.shadowOpacity = 0.9;
                          } else if (el.effectType === 'outline') {
                            styled.stroke = styled.fill || '#111827';
                            styled.strokeWidth = 2;
                          }
                        }
                        return (
                          <EditableText
                            key={el.id}
                            isSelected={isSelected}
                            onSelect={() => handleSelectElement(section.id, el.id)}
                            onChange={(attrs) => updateElement(section.id, el.id, attrs)}
                            shapeProps={styled}
                            hidden={editing?.id === el.id}
                            onStartEdit={(info) => setEditing({ ...info, sectionId: section.id })}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragEnd={() => { setIsDraggingElement(false); setIsAlignedToGrid(false); }}
                          />
                        );
                      }
                      if (el.type === 'image') {
                        return (
                          <URLImage
                            key={el.id}
                            isSelected={isSelected}
                            onSelect={() => handleSelectElement(section.id, el.id)}
                            onChange={(attrs) => updateElement(section.id, el.id, attrs)}
                            shapeProps={el}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragEnd={() => { setIsDraggingElement(false); setIsAlignedToGrid(false); }}
                          />
                        );
                      }
                      if (el.type === 'rect') {
                        return (
                          <EditableRect
                            key={el.id}
                            isSelected={isSelected}
                            onSelect={() => handleSelectElement(section.id, el.id)}
                            onChange={(attrs) => updateElement(section.id, el.id, attrs)}
                            shapeProps={el}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragEnd={() => { setIsDraggingElement(false); setIsAlignedToGrid(false); }}
                          />
                        );
                      }
                      if (el.type === 'circle') {
                        return (
                          <Circle
                            key={el.id}
                            {...el}
                            draggable
                            onClick={() => handleSelectElement(section.id, el.id)}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragMove={onDragSnap}
                            onDragEnd={(e) => { onDragSnap(e); setIsDraggingElement(false); setIsAlignedToGrid(false); }}
                          />
                        );
                      }
                      if (el.type === 'triangle') {
                        return (
                          <RegularPolygon
                            key={el.id}
                            sides={3}
                            radius={Math.max(el.width, el.height) / 2}
                            x={el.x}
                            y={el.y}
                            fill={el.fill}
                            draggable
                            onClick={() => handleSelectElement(section.id, el.id)}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragMove={onDragSnap}
                            onDragEnd={(e) => { onDragSnap(e); setIsDraggingElement(false); setIsAlignedToGrid(false); }}
                          />
                        );
                      }
                      if (el.type === 'polygon' && !el.isStar) {
                        return (
                          <RegularPolygon
                            key={el.id}
                            sides={el.sides}
                            radius={el.outerRadius}
                            x={el.x}
                            y={el.y}
                            fill={el.fill}
                            draggable
                            onClick={() => handleSelectElement(section.id, el.id)}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragMove={onDragSnap}
                            onDragEnd={(e) => { onDragSnap(e); setIsDraggingElement(false); setIsAlignedToGrid(false); }}
                          />
                        );
                      }
                      if (el.type === 'polygon' && el.isStar) {
                        return (
                          <Star
                            key={el.id}
                            numPoints={5}
                            innerRadius={el.innerRadius}
                            outerRadius={el.outerRadius}
                            x={el.x}
                            y={el.y}
                            fill={el.fill}
                            draggable
                            onClick={() => handleSelectElement(section.id, el.id)}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragMove={onDragSnap}
                            onDragEnd={(e) => { onDragSnap(e); setIsDraggingElement(false); setIsAlignedToGrid(false); }}
                          />
                        );
                      }
                      if (el.type === 'line') {
                        return (
                          <Line
                            key={el.id}
                            points={el.points}
                            stroke={el.stroke}
                            strokeWidth={el.strokeWidth}
                            x={el.x}
                            y={el.y}
                            draggable
                            onClick={() => handleSelectElement(section.id, el.id)}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragMove={onDragSnap}
                            onDragEnd={(e) => { onDragSnap(e); setIsDraggingElement(false); setIsAlignedToGrid(false); }}
                          />
                        );
                      }
                      if (el.type === 'arrow') {
                        return (
                          <Arrow
                            key={el.id}
                            points={el.points}
                            stroke={el.stroke}
                            strokeWidth={el.strokeWidth}
                            x={el.x}
                            y={el.y}
                            pointerLength={10}
                            pointerWidth={10}
                            draggable
                            onClick={() => handleSelectElement(section.id, el.id)}
                            onDragStart={() => setIsDraggingElement(true)}
                            onDragEnd={(e) => { onDragSnap(e); setIsDraggingElement(false); }}
                          />
                        );
                      }
                      return null;
                    })}
                  </Layer>
                  </Stage>
                </div>
                {/* Height indicator top-right while dragging */}
                {heightDrag.active && heightDrag.sectionId === section.id && (
                  <div className="absolute top-2 right-2 text-xs px-2 py-1 rounded bg-gray-900 text-white shadow">{Math.round(secHeight)} px</div>
                )}
                {/* Contextual toolbar for non-text inside this section */}
                {selectedId && (() => {
                  const el = (section.elements || []).find((e) => e.id === selectedId);
                  if (!el) return null;
                  const pos = computeSnackbarPos(el);
                  if (el.type !== 'text') {
                    const edit = () => onOpenRightPanel && onOpenRightPanel('position');
                    const duplicate = () => {
                      setSections((prev) => prev.map((s) => {
                        if (s.id !== section.id) return s;
                        const original = (s.elements || []).find((p) => p.id === el.id);
                        if (!original) return s;
                        const copy = { ...original, id: Date.now().toString(), x: (original.x || 0) + 20, y: (original.y || 0) + 20 };
                        return { ...s, elements: [...s.elements, copy] };
                      }));
                      onToast && onToast('Element duplicated');
                    };
                    const del = () => {
                      setSections((prev) => prev.map((s) => (s.id === section.id ? { ...s, elements: (s.elements || []).filter((p) => p.id !== el.id) } : s)));
                      setSelectedId(null);
                      onToast && onToast('Element deleted');
                    };
                    return (
                      <Snackbar
                        visible={!!selectedId}
                        x={pos.x}
                        y={pos.y}
                        onEdit={edit}
                        onDuplicate={duplicate}
                        onDelete={del}
                        onUndo={onUndo}
                      />
                    );
                  }
                  return null;
                })()}
                {/* Inline textarea editor for text elements within this section */}
                {editing && editing.sectionId === section.id && (
                  <InlineTextEditor
                    editing={editing}
                    onChange={(val) => setEditing((e) => ({ ...e, value: val }))}
                    onCommit={() => {
                      updateElement(editing.sectionId, editing.id, { text: editing.value });
                      setEditing(null);
                    }}
                    onCancel={() => setEditing(null)}
                  />
                )}
                {/* Add separator after this section */}
                <div className="mx-auto" style={{ width: canvasSize.width }}>
                  <AddSeparator
                    onAdd={() => addSectionAt(idx, 'after')}
                    onStartHeightDrag={(e) => startHeightDrag(section.id, e.clientY, secHeight)}
                  />
                </div>
              </div>
            );
          })}
        {/* Spacer bottom */}
        <div className="h-6" />
      </div>
    </div>
  );
}

function computeSnackbarPos(el) {
  const pad = 8;
  let x = 0;
  let y = 0;
  if (el.type === 'rect' || el.type === 'text' || el.type === 'image') {
    x = (el.x || 0) + (el.width || 0) + pad;
    y = (el.y || 0) - 28;
  } else if (el.type === 'circle') {
    x = (el.x || 0) + (el.radius || 0) + pad;
    y = (el.y || 0) - (el.radius || 0) - 28;
  } else if (el.type === 'triangle') {
    const r = Math.max(el.width || 0, el.height || 0) / 2;
    x = (el.x || 0) + r + pad;
    y = (el.y || 0) - r - 28;
  } else if (el.type === 'polygon' && !el.isStar) {
    x = (el.x || 0) + (el.outerRadius || 0) + pad;
    y = (el.y || 0) - (el.outerRadius || 0) - 28;
  } else if (el.type === 'polygon' && el.isStar) {
    x = (el.x || 0) + (el.outerRadius || 0) + pad;
    y = (el.y || 0) - (el.outerRadius || 0) - 28;
  } else if (el.type === 'line' || el.type === 'arrow') {
    const pts = el.points || [0, 0, 0, 0];
    const xs = [pts[0], pts[2]];
    const ys = [pts[1], pts[3]];
    x = (el.x || 0) + Math.max(xs[0], xs[1]) + pad;
    y = (el.y || 0) + Math.min(ys[0], ys[1]) - 28;
  }
  return { x, y };
}

function InlineTextEditor({ editing, onChange, onCommit, onCancel }) {
  // position absolutely relative to stage container
  return (
    <div
      style={{
        position: 'absolute',
        left: editing.x,
        top: editing.y,
        width: Math.max(120, editing.width || 200),
        zIndex: 30,
      }}
    >
      <textarea
        autoFocus
        value={editing.value}
        onChange={(e) => onChange(e.target.value)}
        onBlur={onCommit}
        onKeyDown={(e) => {
          if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            onCommit();
          } else if (e.key === 'Escape') {
            onCancel();
          }
        }}
        className="w-full min-h-[60px] text-sm p-2 rounded border shadow focus:outline-none focus:ring-2 focus:ring-indigo-500"
        style={{
          fontSize: editing.fontSize,
          fontFamily: editing.fontFamily,
          lineHeight: '1.2',
          resize: 'vertical',
          background: 'white',
        }}
        placeholder="Ketik teks..."
      />
      <div className="flex gap-2 mt-1 justify-end">
        <button
          onClick={onCommit}
          className="px-2 py-1 text-xs rounded bg-indigo-600 text-white hover:bg-indigo-500"
        >Simpan</button>
        <button
          onClick={onCancel}
          className="px-2 py-1 text-xs rounded bg-gray-200 hover:bg-gray-300"
        >Batal</button>
      </div>
    </div>
  );
}

function AddSeparator({ onAdd, onStartHeightDrag }) {
  return (
    <div className="group relative select-none" style={{ height: 0 }}>
      {/* the line */}
      <div className="absolute left-0 right-0 -translate-y-1/2" style={{ top: 0 }}>
        <div className="border-t border-dashed border-gray-300" />
      </div>
      {/* hover chip */}
      <div className="absolute inset-0 flex justify-center" style={{ top: -14 }}>
        <button
          onClick={onAdd}
          className="px-3 py-1.5 text-xs rounded-full bg-white border shadow-sm hover:bg-gray-50 flex items-center gap-1 opacity-0 transition-opacity duration-150 group-hover:opacity-100"
          style={{ pointerEvents: 'auto' }}
        >
          <span className="w-4 h-4 rounded-full bg-[#FFC72C] text-gray-900 flex items-center justify-center" style={{ lineHeight: 0 }}>
            +
          </span>
          Tambahkan bagian
        </button>
        {/* single drag handle for height */}
        <div
          className="ml-2 w-7 h-7 rounded-full border bg-white hover:bg-gray-50 grid place-items-center shadow-sm cursor-ns-resize opacity-0 transition-opacity duration-150 group-hover:opacity-100 select-none"
          title="Seret untuk atur tinggi"
          onMouseDown={(e) => onStartHeightDrag && onStartHeightDrag(e)}
          style={{ userSelect: 'none' }}
        >
          <span className="text-[10px] leading-none">↕</span>
        </div>
      </div>
    </div>
  );
}
