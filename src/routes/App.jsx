import { Route, Routes } from "react-router-dom";
import Header from "../components/Header";
import Create from "../components/crud/Create";
import Home from "../pages/Home";
import NotFound from "../components/NotFound";

function App() {
  return (
    <>
      <Header />      
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/create" element={<Create />} />
      
        <Route path="*" element={<NotFound />} />
      </Routes>
    </>
  );
}

export default App;
