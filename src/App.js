import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import "./App.css";
import CreateUser from "./components/CreateUser";
import EditUser from "./components/EditUser";
// import Form from "./components/Form";
import ListUser from "./components/ListUser";

function App() {
  return (
    <div className="App">
      <br />
      <h5 className="text-center">
        React CRUD operations using PHP API and MySQL
      </h5>

      <BrowserRouter>
        <nav>
          <ul>
            <li>
              <Link className="btn btn btn-primary" to="/">
                List Users
              </Link>
            </li>
            <li>
              <Link className="btn btn btn-primary" to="user/create">
                Create User
              </Link>
            </li>
          </ul>
        </nav>
        <Routes>
          <Route path="/*" element={<ListUser />} />
          <Route path="user/create" element={<CreateUser />} />
          <Route path="user/:id/edit" element={<EditUser />} />
        </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;
