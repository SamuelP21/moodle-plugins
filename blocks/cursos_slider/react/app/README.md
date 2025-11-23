# ⚛️ Documentación Técnica - React App

Documentación técnica de la aplicación React del plugin **Cursos Slider** para Moodle.

---

## 📋 Tabla de Contenidos

- [Arquitectura](#-arquitectura)
- [Componentes](#-componentes)
- [Hooks Personalizados](#-hooks-personalizados)
- [Flujo de Datos](#-flujo-de-datos)
- [Estilos y Temas](#-estilos-y-temas)
- [Desarrollo](#-desarrollo)
- [Optimización](#-optimización)

---

## 🏗️ Arquitectura

### Stack Tecnológico

```
React 18.2.0
├── Vite 4.5.10 (Build Tool)
├── react-multi-carousel 2.8.5 (Carrusel)
└── CSS3 (Estilos)
```

### Estructura de Carpetas

```
src/
├── App.jsx                    # Componente raíz
├── App.css                    # Estilos globales
├── main.jsx                   # Punto de entrada
│
├── components/                # Componentes de UI
│   ├── CourseAccordion.jsx   # Contenedor principal de secciones
│   ├── CourseCarousel.jsx    # Carrusel genérico reutilizable
│   ├── CoursesActive.jsx     # Sección de cursos activos
│   ├── CoursesActiveDoc.jsx  # Sección de cursos activos (docentes)
│   ├── CoursesEnded.jsx      # Sección de cursos terminados
│   ├── CoursesNext.jsx       # Sección de próximos cursos
│   ├── CoursesStore.jsx      # Sección de cursos archivados
│   ├── GifImage.jsx          # Componente para manejo de GIFs
│   └── ProgressBar.jsx       # Barra de progreso
│
└── hooks/                     # Hooks personalizados
    └── useResponsiveCarousel.js  # Hook para responsive design
```

---

## 🧩 Componentes

### `App.jsx` - Componente Principal

**Responsabilidad**: Punto de entrada de la aplicación React.

```javascript
function App() {
  const [coursesData, setCoursesData] = useState({...});
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Obtener datos desde window.coursesData (inyectado por PHP)
    if (window.coursesData) {
      setCoursesData(window.coursesData);
    }
  }, []);

  return <CourseAccordion {...coursesData} />;
}
```

**Props recibidas de PHP**:

- `is_student`: Boolean - Si el usuario es estudiante
- `courses`: Array - Lista de cursos activos
- `courses_finished`: Array - Cursos completados
- `courses_not_in`: Array - Cursos disponibles
- `image_path`: String - Ruta a assets

---

### `CourseAccordion.jsx` - Contenedor de Secciones

**Responsabilidad**: Organizar y renderizar las diferentes secciones de cursos.

```javascript
const CourseAccordion = ({
  isStudent,
  courses,
  exists_courses,
  exists_courses_finished,
  exists_courses_not_in,
  courses_finished,
  courses_not_in,
  image_path
}) => {
  return (
    <div className="accordion">
      {exists_courses && (
        isStudent 
          ? <CoursesActive courses={courses} />
          : <CoursesActiveDoc courses={courses} />
      )}
      {exists_courses_finished && (
        <CoursesEnded courses={courses_finished} />
      )}
      {exists_courses_not_in && (
        <CoursesNext courses={courses_not_in} />
      )}
    </div>
  );
};
```

**Lógica**:

- Renderiza condicionalmente según existencia de cursos
- Diferencia entre vista de estudiante y docente
- Pasa datos a componentes hijos

---

### `CourseCarousel.jsx` - Carrusel Genérico

**Responsabilidad**: Componente reutilizable de carrusel con responsive design.

```javascript
const CourseCarousel = ({ courses, title, id }) => {
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);

  useEffect(() => {
    // Detectar cuando el drawer de Moodle está abierto
    const checkDrawer = () => {
      const drawer = document.querySelector('body');
      setIsDrawerOpen(drawer.classList.contains('drawer-open'));
    };

    const observer = new MutationObserver(checkDrawer);
    observer.observe(document.body, {
      attributes: true,
      attributeFilter: ['class']
    });

    return () => observer.disconnect();
  }, []);

  return (
    <div className="courses-container">
      <Carousel responsive={responsive}>
        {courses.map(course => (
          <CourseCard key={course.id} course={course} />
        ))}
      </Carousel>
    </div>
  );
};
```

**Características**:

- Detección de drawer de Moodle
- Configuración responsive
- Navegación con flechas
- Soporte para infinite scroll

---

### Componentes de Sección

Cada sección (`CoursesActive`, `CoursesEnded`, etc.) sigue el mismo patrón:

```javascript
const CoursesActive = ({ courses }) => {
  const { containerRef, responsive } = useResponsiveCarousel();
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);

  return (
    <div ref={containerRef}>
      <div className="course__header">
        <h2>CURSOS ACTIVOS</h2>
      </div>
      <Carousel responsive={responsive}>
        {courses.map(course => (
          <CourseCard key={course.id} course={course} />
        ))}
      </Carousel>
    </div>
  );
};
```

**Props**:

- `courses`: Array de objetos de curso

**Estructura de objeto `course`**:

```javascript
{
  id: number,
  fullname: string,
  shortname: string,
  url: string,
  image: string,
  is_gif: boolean,
  progress: number,        // 0-100
  notifications: number,
  is_incomplete: boolean
}
```

---

### `GifImage.jsx` - Manejo de GIFs

**Responsabilidad**: Optimizar la carga y visualización de GIFs.

```javascript
const GifImage = ({ src, isGif, alt, className }) => {
  const [isPlaying, setIsPlaying] = useState(false);
  const [staticImage, setStaticImage] = useState('');

  useEffect(() => {
    if (isGif) {
      // Extraer primer frame del GIF como imagen estática
      const img = new Image();
      img.src = src;
      img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        setStaticImage(canvas.toDataURL());
      };
    }
  }, [src, isGif]);

  return (
    <div 
      className="gif-control"
      onClick={() => setIsPlaying(!isPlaying)}
    >
      {isGif && !isPlaying ? (
        <div className="gif-overlay" style={{ backgroundImage: `url(${staticImage})` }} />
      ) : (
        <img src={src} alt={alt} className={className} />
      )}
    </div>
  );
};
```

**Características**:

- Muestra frame estático por defecto
- Reproduce GIF al hacer clic
- Indicador visual de GIF
- Optimización de rendimiento

---

### `ProgressBar.jsx` - Barra de Progreso

**Responsabilidad**: Visualizar el progreso del curso.

```javascript
const ProgressBar = ({ progress }) => {
  return (
    <div className="progress-container">
      <div 
        className="progress-bar" 
        style={{ width: `${progress}%` }}
        role="progressbar"
        aria-valuenow={progress}
        aria-valuemin="0"
        aria-valuemax="100"
      >
        <span className="progress-text">{progress}%</span>
      </div>
    </div>
  );
};
```

**Props**:

- `progress`: Number (0-100) - Porcentaje de completitud

---

## 🪝 Hooks Personalizados

### `useResponsiveCarousel` - Responsive Design Dinámico

**Responsabilidad**: Adaptar el número de tarjetas según el ancho del contenedor.

```javascript
export const useResponsiveCarousel = () => {
  const containerRef = useRef(null);
  const [containerWidth, setContainerWidth] = useState(0);

  useEffect(() => {
    const updateWidth = () => {
      if (containerRef.current) {
        setContainerWidth(containerRef.current.offsetWidth);
      }
    };

    updateWidth();
    window.addEventListener('resize', updateWidth);
    return () => window.removeEventListener('resize', updateWidth);
  }, []);

  const getItemsCount = () => {
    if (containerWidth > 1200) return 6;
    if (containerWidth > 900) return 4;
    if (containerWidth > 600) return 3;
    if (containerWidth > 400) return 1.5;
    return 1;
  };

  const responsive = {
    desktop: {
      breakpoint: { max: 5000, min: 1024 },
      items: getItemsCount(),
    },
    // ... más breakpoints
  };

  return { containerRef, responsive, containerWidth };
};
```

**Uso**:

```javascript
const MyComponent = () => {
  const { containerRef, responsive } = useResponsiveCarousel();
  
  return (
    <div ref={containerRef}>
      <Carousel responsive={responsive}>
        {/* items */}
      </Carousel>
    </div>
  );
};
```

**Breakpoints**:

- `> 1200px`: 6 tarjetas
- `> 900px`: 4 tarjetas
- `> 600px`: 3 tarjetas
- `> 400px`: 1.5 tarjetas
- `< 400px`: 1 tarjeta

---

## 🔄 Flujo de Datos

### 1. Inicialización

```
PHP (block_cursos_slider.php)
  ↓
  Consulta cursos desde Moodle API
  ↓
  Prepara datos en formato JSON
  ↓
  Inyecta en window.coursesData
  ↓
React (App.jsx)
  ↓
  Lee window.coursesData
  ↓
  Pasa a CourseAccordion
  ↓
  Renderiza componentes de sección
```

### 2. Interacción del Usuario

```
Usuario hace clic en tarjeta
  ↓
Navegación a course.url (Moodle)
  ↓
Moodle maneja la navegación
```

### 3. Responsive Adaptation

```
Cambio de tamaño de ventana
  ↓
useResponsiveCarousel detecta cambio
  ↓
Actualiza containerWidth
  ↓
Recalcula items count
  ↓
Carousel re-renderiza con nueva configuración
```

---

## 🎨 Estilos y Temas

### Variables CSS

```css
:root {
  --primary: #0066cc;
  --secondary: #6c757d;
  --success: #28a745;
  --danger: #dc3545;
  --card-bg: #ffffff;
  --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
```

### Clases Principales

- `.courses-container`: Contenedor principal
- `.carousel__card`: Tarjeta de curso
- `.carousel__img`: Imagen del curso
- `.card-body`: Contenido de la tarjeta
- `.progress-bar`: Barra de progreso
- `.NotificationSpan`: Badge de notificaciones

### Responsive Breakpoints (CSS)

```css
/* Desktop */
@media (min-width: 1024px) { ... }

/* Tablet */
@media (max-width: 1024px) and (min-width: 768px) { ... }

/* Mobile */
@media (max-width: 768px) { ... }
```

---

## 🛠️ Desarrollo

### Comandos Disponibles

```bash
# Instalar dependencias
npm install

# Modo desarrollo (con hot reload)
npm run dev

# Compilar para producción
npm run build

# Preview de producción
npm run preview
```

### Configuración de Vite

```javascript
// vite.config.js
export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'dist',
    rollupOptions: {
      output: {
        entryFileNames: 'assets/[name].js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name].[ext]'
      }
    }
  }
});
```

### Añadir un Nuevo Componente

1. Crear archivo en `src/components/`
2. Importar dependencias necesarias
3. Usar el hook `useResponsiveCarousel` si es un carrusel
4. Exportar el componente
5. Importar en el componente padre

**Ejemplo**:

```javascript
// src/components/MyNewComponent.jsx
import React from 'react';
import { useResponsiveCarousel } from '../hooks/useResponsiveCarousel';

const MyNewComponent = ({ data }) => {
  const { containerRef, responsive } = useResponsiveCarousel();
  
  return (
    <div ref={containerRef}>
      {/* Tu código aquí */}
    </div>
  );
};

export default MyNewComponent;
```

---

## ⚡ Optimización

### Técnicas Implementadas

1. **Lazy Loading de Imágenes**
   - Las imágenes se cargan solo cuando son visibles
   - Uso de `loading="lazy"` en tags `<img>`

2. **Memoización**
   - `useMemo` para cálculos costosos
   - `useCallback` para funciones en dependencias

3. **Code Splitting**
   - Vite automáticamente divide el código
   - Chunks separados para vendor y app

4. **Optimización de GIFs**
   - Frame estático por defecto
   - Reproducción bajo demanda

5. **Debouncing**
   - Resize events con debounce
   - Evita re-renders innecesarios

### Métricas de Rendimiento

- **Bundle Size**: ~204 KB (gzipped: ~59 KB)
- **First Contentful Paint**: < 1s
- **Time to Interactive**: < 2s

---

## 🐛 Debugging

### Console Logs

Para debug, descomenta en `useResponsiveCarousel.js`:

```javascript
console.log('Container width:', containerWidth);
```

### React DevTools

1. Instala [React DevTools](https://react.dev/learn/react-developer-tools)
2. Abre las herramientas de desarrollo
3. Ve a la pestaña "Components"
4. Inspecciona el árbol de componentes

### Common Issues

**El carrusel no se adapta al contenedor**:

- Verifica que `containerRef` esté asignado
- Revisa que `useResponsiveCarousel` esté importado
- Comprueba los breakpoints en el hook

**Las imágenes no cargan**:

- Verifica la ruta en `window.coursesData.image_path`
- Comprueba permisos de archivos en Moodle
- Revisa la consola por errores CORS

**¿Preguntas?** Consulta el [README principal](../README.md) o abre un issue en GitHub.
