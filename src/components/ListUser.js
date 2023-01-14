import axios from "axios";
import { useEffect, useState } from "react";
import { Link } from "react-router-dom";

export default function ListUser() {
  const [state, setState] = useState([]);
  const [userList, setUserList] = useState("100");

  useEffect(() => {
    getUsers();
  }, []);

  function getUsers() {
    axios.get("http://192.168.0.186/api/customers").then(function (response) {
      console.log(response.data);
      setState(response.data.response.data);
    });
  }

  const deleteUser = (id) => {
    console.log(id);
    axios.delete(`http://192.168.0.186/api/customers?customerId=${id}`)
      .then(function (response) {
        console.log(response.data);
        alert("Deleted successfully!");
        getUsers();
        // setState();
      });
  };

  return (
    <div className="container-fluid">
      <div className="row">
        <div className="col-lg-6 col-md-6 col-sm-12">
          <h3>List Customers</h3>
        </div>
        <div className="col-lg-6 col-md-6 col-sm-12">
          <div style={{ textAlign: "right" }}><p className="text-right">Add User <b>{userList}</b></p></div>
        </div>
      </div>
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
                  className="btn btn-sm btn-success"
                >
                  Add
                </button>
                <Link
                  to={`/user/${user.customerId}/edit`}
                  className="mx-1 btn btn-sm btn-primary"
                >
                  Edit
                </Link>
                <button
                  onClick={() => deleteUser(user.customerId)}
                  className="btn btn-danger btn-sm m-1"
                >
                  Delete
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

