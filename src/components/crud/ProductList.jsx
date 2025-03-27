import axios from "axios";
import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { Api, Url } from "../../config/Api";
import { Fade } from "react-awesome-reveal";
import { Card } from "react-bootstrap";
import { Button } from "react-bootstrap";
import Swal from "sweetalert2";

const ProductList = () => {
  const [products, setProducts] = useState([]);

  useEffect(() => {
    getProducts();
  }, []);

  const getProducts = async () => {
    const res = await axios.get(Api);
    console.log(res.data);
    setProducts(res.data);
  };

  const deleteProducts = async (productId) => {
    try {
      console.log(productId);

      await Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "btn-outline-primary",
        confirmButtonText: "Delete",
      }).then(async (result) => {
        if (result.isConfirmed) {
          // Call Delete API
          await axios.delete(`${Api}/${productId}`);
          getProducts();

          Swal.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success",
          });
        }
      });
    } catch (error) {
      console.log(error);
    }
  };

  return (
    <div className="container">
      <div className="d-flex justify-content-between align-items-center mt-2">
        <h1 className="text-center">Product List</h1>
        <Link to={"/create"} className="btn btn-outline-primary">
          Add Product
        </Link>
      </div>
      <hr />
      <div className="row">
        {products.map((product) => {
          // console.log("Image URL:", product.image);
          return (
            <div key={product.id} className="col-md-4 mb-3">
              <Fade>
                <Card className="shadow">
                  <Card.Img
                    className="img-fluid"
                    variant="top"
                    src={`${Url}/${product.image}`}
                    style={{ height: "200px", objectFit: "contain" }}
                  />
                  <Card.Body>
                    <Card.Title className="text-center">
                      {product.name}
                    </Card.Title>
                    <Card.Text className="text-center">
                      {product.description}
                    </Card.Text>
                    <Card.Text className="text-center">
                      {product.price}
                    </Card.Text>
                    <div className="d-flex justify-content-center gap-2 my-2">
                      <Link
                        to={`/edit/${product.id}`}
                        className="btn btn-outline-primary"
                      >
                        Edit
                      </Link>
                      <Button
                        variant="danger"
                        onClick={() => deleteProducts(product.id)}
                      >
                        Delete
                      </Button>
                    </div>
                  </Card.Body>
                </Card>
              </Fade>
            </div>
          );
        })}
      </div>
    </div>
  );
};

export default ProductList;
