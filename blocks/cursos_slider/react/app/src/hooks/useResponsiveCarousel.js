import { useState, useEffect, useRef } from 'react';

/**
 * Hook personalizado para obtener configuración responsive del carrusel
 * basada en el ancho del contenedor
 */
export const useResponsiveCarousel = () => {
    const containerRef = useRef(null);
    const [containerWidth, setContainerWidth] = useState(0);

    useEffect(() => {
        const updateWidth = () => {
            if (containerRef.current) {
                setContainerWidth(containerRef.current.offsetWidth);
            }
        };

        // Actualizar al montar
        updateWidth();

        // Actualizar al redimensionar
        window.addEventListener('resize', updateWidth);

        // Cleanup
        return () => window.removeEventListener('resize', updateWidth);
    }, []);

    // Función para calcular items según ancho del contenedor
    const getItemsCount = () => {
        if (containerWidth > 1200) return 6;
        if (containerWidth > 900) return 4;
        if (containerWidth > 600) return 3;
        if (containerWidth > 400) return 1.5;
        if (containerWidth > 200) return 1;
        return 1;
    };

    // Configuración responsive dinámica
    const responsive = {
        superLarge: {
            breakpoint: { max: 4000, min: 1200 },
            items: getItemsCount(),
            slidesToSlide: 1,
        },
        desktop: {
            breakpoint: { max: 1200, min: 900 },
            items: getItemsCount(),
            slidesToSlide: 1,
        },
        tablet: {
            breakpoint: { max: 900, min: 600 },
            items: getItemsCount(),
            slidesToSlide: 1,
        },
        mobile: {
            breakpoint: { max: 600, min: 400 },
            items: getItemsCount(),
            slidesToSlide: 1,
        },
        mobilexs: {
            breakpoint: { max: 400, min: 0 },
            items: 1,
            slidesToSlide: 1,
        }
    };

    return { containerRef, responsive, containerWidth };
};