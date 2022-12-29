import axios from "axios";
import { useEffect, useState } from "react";

export default function ListUser() {
  const [state, setState] = useState([]);

  useEffect(() => {
    getUsers();
  }, []);

  function getUsers() {
    // axios.get("http://192.168.0.158/api/customers").then(function (response) {
    axios.get("http://192.168.184.189/api/customers").then(function (response) {
      console.log(response.data);
      setState(response.data.response.data);
    });
  }

  const deleteUser = (id) => {
    console.log(id);
    axios
      // .delete(`http://192.168.0.158/api/customers?customerId=${id}`)
      .delete(`http://192.168.184.189/api/customers?customerId=${id}`)
      .then(function (response) {
        console.log(response.data);
        alert("Deleted successfully!");
        getUsers();
        // setState();
      });
  };

  return (
    <div className="container-fluid">
      <h1>List Customers</h1>
      <table width="100%" className="table table-hover">
        <thead>
          <tr className="bg-secondary text-white">
            <th>Id</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Address</th>
            <th>Created Date</th>
            <th>Operations</th>
          </tr>
        </thead>
        <tbody>
          {state.map((user, key) => (
            <tr key={key}>
              <td>{user.customerId}</td>
              <td>{user.name}</td>
              <td>{user.mobileNo}</td>
              <td>{user.email}</td>
              <td>{user.address}</td>
              <td>{user.insertedAt}</td>
              <td>
                <button
                  onClick={() => deleteUser(user.customerId)}
                  className="btn btn-danger btn-sm m-1"
                >
                  Delete
                </button>
                <button
                  to={`user/${user.customerId}/edit`}
                  className="btn btn-sm btn-primary"
                >
                  Edit
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
