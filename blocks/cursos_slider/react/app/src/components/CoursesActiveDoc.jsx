import React, { useState, useEffect } from "react";
import Carousel from "react-multi-carousel";
import "react-multi-carousel/lib/styles.css";
import ProgressBar from './ProgressBar';
import GifImage from './GifImage';
import { useResponsiveCarousel } from '../hooks/useResponsiveCarousel';

const CourseCard = ({ course }) => {
  return (
    <a href={course.url} key={course.id}>
      <div className="card carousel__card">
        <div className="carousel__img-container">
          {course.is_gif ? (
            <GifImage
              src={course.image}
              isGif={true}
              alt={course.shortname || course.fullname}
              className="carousel__img"
            />
          ) : (
            <img
              className="carousel__img"
              src={course.image}
              alt={course.shortname || course.fullname}
            />
          )}
        </div>
        <ProgressBar progress={course.progress || 0} />
        <div className="card-body">
          <h3 className="card-titles carousel__par h5">
            <span>{course.fullname}</span>
          </h3>
          <p className="d-flex justify-content-center btn btn-primary px-1 py-2">
            IR AL CURSO
          </p>
          <span className="badge badge-circle NotificationSpan" aria-valuenow={course.notifications}>
            {course.notifications}
          </span>
        </div>
      </div>
    </a>
  );
};

const CoursesActiveDoc = ({ courses }) => {
  const { containerRef, responsive } = useResponsiveCarousel();
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);

  useEffect(() => {
    // Detectar cuando el drawer está abierto
    const checkDrawer = () => {
      const drawer = document.querySelector('body');
      setIsDrawerOpen(drawer.classList.contains('drawer-open'));
    };

    // Observar cambios en el body
    const observer = new MutationObserver(checkDrawer);
    observer.observe(document.body, {
      attributes: true,
      attributeFilter: ['class']
    });

    // Verificación inicial
    checkDrawer();

    return () => observer.disconnect();
  }, []);

  return (
    <div ref={containerRef}>
      <div className="course__header" id="headingOne" aria-expanded="true">
        <h2 className="mb-0 carousel__title">
          <button className="text-left course__button" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            CURSOS ACTIVOS
            <svg width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1.56977 16C1.09884 16 0.719477 15.8298 0.431686 15.4894C0.143895 15.1489 0 14.7518 0 14.2979C0 14.1844 0.156977 13.7872 0.47093 13.1064L11.8517 0.765957C12.1134 0.48227 12.375 0.283688 12.6366 0.170213C12.8983 0.0567376 13.186 0 13.5 0C13.814 0 14.1017 0.0567376 14.3634 0.170213C14.625 0.283688 14.8866 0.48227 15.1483 0.765957L26.5291 13.1064C26.686 13.2766 26.8038 13.461 26.8823 13.6596C26.9608 13.8582 27 14.0709 27 14.2979C27 14.7518 26.8561 15.1489 26.5683 15.4894C26.2805 15.8298 25.9012 16 25.4302 16L1.56977 16Z" fill="var(--primary)" />
            </svg>
          </button>
        </h2>
      </div>

      <div id="collapseOne" className="accordion-collapse collapse show" aria-labelledby="headingOne">
        <Carousel
          swipeable={true}
          draggable={true}
          showDots={false}
          responsive={responsive}
          infinite={courses.length > 3}
          autoPlay={false}
          keyBoardControl={true}
          customTransition="all .5s"
          transitionDuration={500}
          containerClass="carousel-container"
          removeArrowOnDeviceType={["mobile", "mobilexs"]}
          itemClass="carousel-item-padding-40-px"
        >
          {courses.map((course) => (
            <CourseCard key={course.id} course={course} />
          ))}
        </Carousel>
      </div>
    </div>
  );
};

export default CoursesActiveDoc;
