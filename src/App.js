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
              <Link to="/">List Users</Link>
            </li>
            <li>
              <Link to="user/create">Create User</Link>
            </li>
          </ul>
        </nav>
        <Routes>
          <Route index element={<ListUser />} />
          <Route path="user/create" element={<CreateUser />} />
          <Route path="user/:id/edit" element={<EditUser />} />
        </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;
