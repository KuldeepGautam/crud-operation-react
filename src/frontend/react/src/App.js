import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import "./App.css";
import CreateCustomer from "./components/CreateCustomer";
// import Form from "./components/Form";
import CustomerList from "./components/CustomerList";
import EditCustomer from "./components/EditCustomer";

function App() {
  return (
    <div className="container App">
      <br />
      <h2 className="text-center">
        Unlimited <span className="text-danger">movies</span>, TV shows and more
      </h2>
      <BrowserRouter>
        <div class="text-center">
          <nav>
            <ul>
              <li>
                <Link className="btn btn btn-warning" to="/">
                  Movies List
                </Link>
              </li>
              <li>
                <Link className="btn btn btn-primary" to="customer/create">
                  Create New Movies
                </Link>
              </li>
              <li>
                <Link className="btn btn btn-danger" to="user/create">
                  Login
                </Link>
              </li>
            </ul>
          </nav>
        </div>
        <Routes>
          <Route path="/*" element={<CustomerList />} />
          <Route path="customer/create" element={<CreateCustomer />} />
          <Route path="customer/:id/edit" element={<EditCustomer />} />
        </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;
