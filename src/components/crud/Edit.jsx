import axios from "axios";
import React from "react";
import { useState } from "react";
import { Col, Container, Figure } from "react-bootstrap";
import { Api, Url } from "../../config/Api";
import { useNavigate, useParams } from "react-router-dom";
import Swal from "sweetalert2";
import { useEffect } from "react";

const Edit = () => {
  const [preview, setPreview] = useState();
  const [previewName, setPreviewName] = useState("");
  const [image, setImage] = useState("");
  const [name, setName] = useState("");
  const [description, setDescription] = useState("");
  const [price, setPrice] = useState("");
  const [errors, setErrors] = useState([]);

  // For navigation
  const navigate = useNavigate();
  const { id } = useParams();

  useEffect(() => {
    getProducts();
  }, []);

  const getProducts = async () => {
    const res = await axios.get(`${Api}/${id}`);
    console.log(res.data.data);
    setName(res.data.data.name);
    setDescription(res.data.data.description);
    setPrice(res.data.data.price);
    setPreview(`${Url}/${res.data.data.image}`);
    setPreviewName(res.data.data.image);
  };

  const loadImage = (e) => {
    // console.log(e.target.files[0]);
    const img = e.target.files[0];
    if (img && img.type.startsWith("image/")) {
      setImage(img);
      setPreview(URL.createObjectURL(img));
      setPreviewName(img.name);
    } else {
      setImage(null);
      setPreview(null);
      setPreviewName("");
    }
  };

  const updateProduct = async (e) => {
    e.preventDefault();
    const formData = new FormData();
    formData.append("image", image);
    formData.append("name", name);
    formData.append("description", description);
    formData.append("price", price);
    formData.append("_method", "PUT");

    console.log("Data yang dikirim:");
    for (let pair of formData.entries()) {
      console.log(pair[0] + ": " + pair[1]);
    }

    try {
      await axios.post(`${Api}/${id}`, formData, {
        headers: {
          Accept: "application/json",
          "Content-Type": "multipart/form-data",
        },
      });
      Swal.fire({
        title: "Your data has been updated!",
        icon: "success",
        showConfirmButton: false,
        timer: 1500,
      });
      navigate("/");
    } catch (error) {
      console.log(error.response.status);
      if (error.response.status === 422) {
        console.log(error.response.data.errors);
        setErrors(error.response.data.errors);
      }
    }
  };

  return (
    <>
      <Container className="mt-3">
        <h3 className="text-center">Edit Product Form</h3>
        <hr />

        <div className="mt-3 d-lg-flex flex-lg-row justify-content-center d-sm-flex flex-sm-column">
          <Col className="col-lg-6">
            <form onSubmit={updateProduct}>
              <div className="form-group my-3">
                <label>Product Name</label>
                <input
                  className="form-control"
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                />
                <div className="text-danger">
                  {errors.name && (
                    <div className="text-danger">
                      <small>{errors.name}</small>
                    </div>
                  )}
                </div>
              </div>
              <div className="form-group my-3">
                <label>Description</label>
                <input
                  className="form-control"
                  type="text"
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                />
                {errors.description && (
                  <div className="text-danger">
                    <small>{errors.description}</small>
                  </div>
                )}
              </div>
              <div className="form-group my-3">
                <label>Price</label>
                <input
                  className="form-control"
                  type="text"
                  value={price}
                  onChange={(e) => setPrice(e.target.value)}
                />
                {errors.price && (
                  <div className="text-danger">
                    <small>{errors.price}</small>
                  </div>
                )}
              </div>
              <div className="form-group my-3">
                <label>Image</label>
                <input
                  className="form-control"
                  type="file"
                  onChange={loadImage}
                />
                {errors.image && (
                  <div className="text-danger">
                    {errors.image.map((errMsg, index) => (
                      <small key={index} className="d-block">
                        {errMsg}
                      </small>
                    ))}
                  </div>
                )}
              </div>
              <div>
                <div>
                  <button className="btn btn-primary" type="submit">
                    Edit Product
                  </button>
                  <button
                    className="btn btn-outline-secondary mx-2"
                    onClick={() => navigate("/")}
                  >
                    Back
                  </button>
                </div>
              </div>
            </form>
          </Col>

          {/* Show Image */}
          {preview && (
            <Col className="col-lg-5 text-center">
              <Figure>
                <Figure.Image
                  width="100%"
                  style={{ height: 300 }}
                  alt={previewName}
                  src={preview}
                  className="img-thumbnail"
                ></Figure.Image>
                <Figure.Caption>{previewName}</Figure.Caption>
              </Figure>
            </Col>
          )}
        </div>
      </Container>
    </>
  );
};

export default Edit;
