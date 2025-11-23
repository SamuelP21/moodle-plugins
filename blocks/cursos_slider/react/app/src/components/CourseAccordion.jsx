import React from "react";
import CoursesActive from "./CoursesActive";
import CoursesActiveEmpty from "./CoursesActiveEmpty";
import CoursesEnded from "./CoursesEnded";
import CoursesEndedEmpty from "./CoursesEndedEmpty";
import CoursesNext from "./CoursesNext";
import CoursesNextEmpty from "./CoursesNextEmpty";
import CoursesActiveDoc from "./CoursesActiveDoc";
import CoursesStore from "./CoursesStore";

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
    <div>
      {isStudent ? (
        // Vista de estudiante
        <>
          {/* Cursos Activos */}
          {exists_courses ? (
            <CoursesActive courses={courses.filter(course => course.is_incomplete)} />
          ) : (
            <CoursesActiveEmpty image_path={image_path} />
          )}

          {/* Cursos Terminados */}
          {exists_courses_finished ? (
            <CoursesEnded courses={courses_finished} />
          ) : (
            <CoursesEndedEmpty image_path={image_path} />
          )}

          {/* Próximos Cursos */}
          {exists_courses_not_in && courses_not_in?.length > 0 ? (
            <CoursesNext courses={courses_not_in} />
          ) : (
            <CoursesNextEmpty image_path={image_path} />
          )}
        </>
      ) : (
        // Vista de docente
        <>
          <CoursesActiveDoc 
            courses={courses.filter(course => 
              course.is_incomplete && !course.completed_courses_date
            )} 
          />
          <CoursesStore 
            courses={courses.filter(course => 
              course.completed_courses_date
            )} 
          />
        </>
      )}
    </div>
  );
};

export default CourseAccordion;
