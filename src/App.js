import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import "./App.css";
import CreateCustomer from "./components/CreateCustomer";
// import Form from "./components/Form";
import CustomerList from "./components/CustomerList";
import EditCustomer from "./components/EditCustomer";

function App() {
  return (
    <div className="container text-center App">
      <br />
      <h2 className="text-center">React CRUD operations</h2>
      <BrowserRouter>
        <nav>
          <ul>
            <li>
              <Link className="btn btn btn-primary" to="/">
                Customer List
              </Link>
            </li>
            <li>
              <Link className="btn btn btn-primary" to="customer/create">
                Create New Customer
              </Link>
            </li>
            <li>
              <Link className="btn btn btn-primary" to="user/create">
                Registration
              </Link>
            </li>
            <li>
              <Link className="btn btn btn-primary" to="user/create">
                Login
              </Link>
            </li>
          </ul>
        </nav>
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
