import React, { useState, useEffect } from "react";

const CoursesNextEmpty = () => {
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
      <div className="course__header" id="headingThree" aria-expanded="true">
        <h2 className="mb-0 carousel__title">
          <button className="text-left course__button" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
            PRÓXIMOS CURSOS
            <svg width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1.56977 16C1.09884 16 0.719477 15.8298 0.431686 15.4894C0.143895 15.1489 0 14.7518 0 14.2979C0 14.1844 0.156977 13.7872 0.47093 13.1064L11.8517 0.765957C12.1134 0.48227 12.375 0.283688 12.6366 0.170213C12.8983 0.0567376 13.186 0 13.5 0C13.814 0 14.1017 0.0567376 14.3634 0.170213C14.625 0.283688 14.8866 0.48227 15.1483 0.765957L26.5291 13.1064C26.686 13.2766 26.8038 13.461 26.8823 13.6596C26.9608 13.8582 27 14.0709 27 14.2979C27 14.7518 26.8561 15.1489 26.5683 15.4894C26.2805 15.8298 25.9012 16 25.4302 16L1.56977 16Z" fill="var(--primary)"/>
            </svg>
          </button>
        </h2>
      </div>

      <div id="collapseThree" className="accordion-collapse collapse show" aria-labelledby="headingThree">
        <div className="coursesEmpty__message">
          <h3>No hay próximos cursos disponibles en este momento</h3>
        </div>
      </div>
    </>
  );
};

export default CoursesNextEmpty;
