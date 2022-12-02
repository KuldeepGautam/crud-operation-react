import axios from "axios";
import { useEffect, useState } from "react";

export default function ListUser() {
  const [state, setState] = useState([]);

  useEffect(() => {
    getUsers();
  }, []);

  function getUsers() {
<<<<<<< HEAD
    axios.get("http://192.168.184.189/api/customers").then(function (response) {
      // axios.get("http://192.168.184.189/api/customers").then(function (response) {
      // console.log(response.data);
=======
    axios.get("http://192.168.0.158/api/customers").then(function (response) {
      console.log(response.data);
>>>>>>> 3b10024d211ac8f793b3802cabb6b499b9aad842
      setState(response.data.response.data);
    });
  }

  const deleteUser = (id) => {
    console.log(id);
    axios
      .delete(`http://192.168.0.158/api/customers?customerId=${id}`)
      .then(function (response) {
<<<<<<< HEAD
        // console.log(response.data);
        setState();
=======
        console.log(response.data);
        alert("Deleted successfully!");
        getUsers();
        // setState();
>>>>>>> 3b10024d211ac8f793b3802cabb6b499b9aad842
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
<<<<<<< HEAD
                <button className="m-1 btn btn-danger btn-sm">Delete</button>
                <button className="btn btn-primary btn-sm">Edit</button>
=======
                <button
                  onClick={() => deleteUser(user.customerId)}
                  className="btn btn-danger btn-sm m-1"
                >
                  Delete
                </button>
                <button className="btn btn-sm btn-primary">Edit</button>
>>>>>>> 3b10024d211ac8f793b3802cabb6b499b9aad842
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
