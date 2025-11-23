import React, { useState, useEffect } from "react";

const CoursesActiveEmpty = () => {
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);

  useEffect(() => {
    const checkDrawer = () => {
      const drawer = document.querySelector('body');
      setIsDrawerOpen(drawer.classList.contains('drawer-open'));
    };

    const observer = new MutationObserver(checkDrawer);
    observer.observe(document.body, {
      attributes: true,
      attributeFilter: ['class']
    });

    checkDrawer();
    return () => observer.disconnect();
  }, []);

  return (
    <>
      <div className="course__header" id="headingOne" aria-expanded="true">
        <h2 className="mb-0 carousel__title">
          <button className="text-left course__button" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            CURSOS ACTIVOS
            <svg width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1.56977 16C1.09884 16 0.719477 15.8298 0.431686 15.4894C0.143895 15.1489 0 14.7518 0 14.2979C0 14.1844 0.156977 13.7872 0.47093 13.1064L11.8517 0.765957C12.1134 0.48227 12.375 0.283688 12.6366 0.170213C12.8983 0.0567376 13.186 0 13.5 0C13.814 0 14.1017 0.0567376 14.3634 0.170213C14.625 0.283688 14.8866 0.48227 15.1483 0.765957L26.5291 13.1064C26.686 13.2766 26.8038 13.461 26.8823 13.6596C26.9608 13.8582 27 14.0709 27 14.2979C27 14.7518 26.8561 15.1489 26.5683 15.4894C26.2805 15.8298 25.9012 16 25.4302 16L1.56977 16Z" fill="var(--primary)"/>
            </svg>
          </button>
        </h2>
      </div>

      <div id="collapseOne" className="accordion-collapse collapse show" aria-labelledby="headingOne">
        <div className="card w-100 d-block coursesEmpty">
          <img 
            src={`${image_path}/Alumnos.png`} 
            className="card-img-top carousel__img coursesEmpty__image" 
            alt="Kids"
          />
          <div className="card-body coursesEmpty__message">
            <h3 className="card-titles">
              Vaya... No tienes cursos activos, ¡echa un vistazo a nuestro catálogo de cursos!
            </h3>
          </div>
        </div>
      </div>
    </>
  );
};

export default CoursesActiveEmpty;
