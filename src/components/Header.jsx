import Container from "react-bootstrap/Container";
import Nav from "react-bootstrap/Nav";
import Navbar from "react-bootstrap/Navbar";
import { Link } from "react-router-dom";

function Header() {
  const kebakaran = "/images/fire.png"; // Gambar yang akan diulang
  const countFire = 5;

  return (
    <Navbar expand="lg" bg="primary" variant="dark">
      <Container>
        <Navbar.Brand as={Link} to="/">
          <img
            src="/images/fire.png"
            alt="Fire"
            width="30"
            height="30"
            className="mx-2"
          />
          <strong>Kebakaran</strong>
          {Array(countFire)
            .fill(kebakaran)
            .map((icon, index) => (
              <img
                key={index}
                src={icon}
                alt={`Fire Icon ${index + 1}`}
                width="30"
                height="30"
                className="mx-2"
              />
            ))}
        </Navbar.Brand>
        <Navbar.Toggle aria-controls="basic-navbar-nav" />
        <Navbar.Collapse id="basic-navbar-nav">
          <Nav className="ms-auto">
            <Nav.Link as={Link} to="/">
              Home
            </Nav.Link>
            <Nav.Link as={Link} to="/information">
              Information
            </Nav.Link>
            <Nav.Link as={Link} to="/service">
              Service
            </Nav.Link>
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
}

export default Header;
