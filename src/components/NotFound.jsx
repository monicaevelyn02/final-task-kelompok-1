import React from "react";
import { Link } from "react-router-dom";

const NotFound = () => {
  return (
    <>
      <div className="container d-flex justify-content-center flex-column align-items-center">
        <div className="col-md-5">
          <img className="img-fluid mt-4" src="images/404.svg" alt="404" />
        </div>
        <div className="col-md-6 d-flex flex-column align-items-center">
          <Link to="/" className="btn btn-primary mt-4">
            Go Back to Home
          </Link>
          <h1 className="text-center mt-2">404 - Page Not Found</h1>
        </div>
      </div>
    </>
  );
};

export default NotFound;
