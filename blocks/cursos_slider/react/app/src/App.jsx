import React, { useState, useEffect } from "react";
import CourseAccordion from "./components/CourseAccordion";
import "react-multi-carousel/lib/styles.css";
import "./App.css";
import CoursesNext from "./components/CoursesNext";
import CoursesNextEmpty from "./components/CoursesNextEmpty";

function App() {
  const [coursesData, setCoursesData] = useState({
    is_student: false,
    courses: [],
    exists_courses: false,
    exists_courses_finished: false,
    exists_courses_not_in: false,
    courses_finished: [],
    courses_not_in: [],
    image_path: ''
  });

  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    try {
      if (window.coursesData) {
        setCoursesData(window.coursesData);
      } else {
        setError('No se encontraron datos de cursos');
      }
    } catch (err) {
      setError(err.message);
    } finally {
      setIsLoading(false);
    }
  }, []);

  if (isLoading) return <div>Cargando...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div className="courses-container">
      <CourseAccordion
        isStudent={coursesData.is_student}
        courses={coursesData.courses}
        exists_courses={coursesData.exists_courses}
        exists_courses_finished={coursesData.exists_courses_finished}
        exists_courses_not_in={coursesData.exists_courses_not_in}
        courses_finished={coursesData.courses_finished}
        courses_not_in={coursesData.courses_not_in}
        image_path={coursesData.image_path}
      />
    </div>
  );
}

export default App;