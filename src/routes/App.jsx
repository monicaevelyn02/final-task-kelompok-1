import { Route, Routes } from "react-router-dom";
import Header from "../components/Header";
import Create from "../components/crud/Create";
import Home from "../pages/Home";
import NotFound from "../components/NotFound";
import Edit from "../components/crud/Edit";
import Information from "../pages/Information";
import Service from "../pages/Service";

function App() {
  return (
    <>
      <Header />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/information" element={<Information />} />
        <Route path="/service" element={<Service />} />

        <Route path="*" element={<NotFound />} />

        {/* CRUD */}
        <Route path="/create" element={<Create />} />
        <Route path="/edit/:id" element={<Edit />} />
      </Routes>
    </>
  );
}

export default App;
