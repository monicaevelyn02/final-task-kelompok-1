import axios from "axios";
import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { Api, Url } from "../../config/Api";
import { Fade } from "react-awesome-reveal";
import { Card } from "react-bootstrap";
import { Button } from "react-bootstrap";

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
          console.log("Image URL:", product.image);
          return (
            <div key={product.id} className="col-md-4 mb-3">
              <Fade>
                <Card>
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
                    <div className="btn-group d-flex justify-content-center gap-2 my-2 mx-2">
                      <Link
                        to={`/edit/${product.id}`}
                        className="btn btn-outline-primary"
                      >
                        Edit
                      </Link>
                      <Button
                        variant="outline-danger"
                        // onClick={() => deleteContact(contact.id)}
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
