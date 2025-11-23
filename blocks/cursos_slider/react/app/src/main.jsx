import React from 'react'
import ReactDOM from 'react-dom/client'
import "react-multi-carousel/lib/styles.css"; // Primero los estilos del carrusel
import App from './App'
import './App.css' // Después nuestros estilos personalizados

// Simular el entorno de Moodle en desarrollo
if (process.env.NODE_ENV === 'development') {
  window.M = window.M || {};
  window.M.cfg = window.M.cfg || {};
  window.M.cfg.wwwroot = 'http://localhost:3000';
  window.help_popups = {};
  window.js_pending = () => {};
}

// Función para inicializar la aplicación
function initializeApp(containerId = 'cursos-slider-root') {
  console.log('Iniciando aplicación en:', containerId);
  
  const rootElement = document.getElementById(containerId);
  
  if (!rootElement) {
    console.error(`No se encontró el elemento con id "${containerId}"`);
    return;
  }

  // Limpiar el elemento antes de renderizar
  while (rootElement.firstChild) {
    rootElement.removeChild(rootElement.firstChild);
  }

  try {
    ReactDOM.createRoot(rootElement).render(
      <React.StrictMode>
        <App />
      </React.StrictMode>
    );
    console.log('Aplicación renderizada exitosamente');
  } catch (error) {
    console.error('Error al renderizar la aplicación:', error);
  }
}

// Exportar la función de inicialización
window.initializeCoursesSlider = initializeApp;

// Solo en desarrollo
if (process.env.NODE_ENV === 'development') {
  // Simular los datos que vendrían de Moodle
  window.coursesData = {
    is_student: true,
    courses: [],
    exists_courses: false,
    exists_courses_finished: false,
    exists_courses_not_in: false,
    courses_finished: [],
    courses_not_in: [],
    image_path: '/src/assets'
  };

  // Crear elemento root si no existe
  if (!document.getElementById('root')) {
    const root = document.createElement('div');
    root.id = 'root';
    document.body.appendChild(root);
  }
  
  // Inicializar la app
  initializeApp('root');
}