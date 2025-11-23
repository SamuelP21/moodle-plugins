import { useState, useEffect, useRef } from 'react';

const GifImage = ({ src, isGif, alt, className }) => {
    const [isPlaying, setIsPlaying] = useState(false);
    const [staticImage, setStaticImage] = useState(null);
    const imageRef = useRef(null);
    const gifRef = useRef(null);

    useEffect(() => {
        if (isGif) {
            // Crear una imagen oculta para el GIF
            const gifImg = new Image();
            gifImg.src = src;
            gifRef.current = gifImg;

            // Crear la imagen estática
            const staticImg = new Image();
            staticImg.src = src;
            staticImg.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                canvas.width = staticImg.width;
                canvas.height = staticImg.height;
                
                ctx.drawImage(staticImg, 0, 0);
                
                try {
                    const staticUrl = canvas.toDataURL('image/png');
                    setStaticImage(staticUrl);
                    if (imageRef.current) {
                        imageRef.current.src = staticUrl;
                    }
                } catch(e) {
                    console.error('Error al crear imagen estática:', e);
                }
            };
        }
    }, [src, isGif]);

    const handleMouseEnter = () => {
        if (isGif && gifRef.current && imageRef.current) {
            console.log('Mouse enter'); // Para debug
            imageRef.current.src = src; // Usar directamente el src original
            setIsPlaying(true);
        }
    };

    const handleMouseLeave = () => {
        if (isGif && staticImage && imageRef.current) {
            console.log('Mouse leave'); // Para debug
            imageRef.current.src = staticImage;
            setIsPlaying(false);
        }
    };

    const handleTouch = (e) => {
        if (!isGif) return;
        
        e.preventDefault();
        setIsPlaying(!isPlaying);
    };

    const isTouchDevice = () => {
        return ('ontouchstart' in window) || 
               (navigator.maxTouchPoints > 0) || 
               (navigator.msMaxTouchPoints > 0);
    };

    return (
        <div 
            className={`gif-container ${className || ''}`}
            onMouseEnter={handleMouseEnter}
            onMouseLeave={handleMouseLeave}
            onClick={isTouchDevice() ? handleTouch : undefined}
        >
            <img
                ref={imageRef}
                src={src}
                alt={alt}
                className="original-image"
            />
        </div>
    );
};

export default GifImage; 