import axios from "axios";
import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import config from "../config";

export default function MovieList() {
  const [state, setState] = useState([
    {
      searchText: "",
    },
  ]);
  const apiUrl = config.apiUrl;

  useEffect(() => {
    getUsers();
  }, []);

  function getUsers() {
    const { searchText } = state;
    // const { data } = await getUsers( searchText);

    axios.get(apiUrl).then(function (response) {
      console.log(response.data);
      setState(response.data.response.data);
    });
  }

  const deleteUser = (id) => {
    console.log(id);
    axios.delete(apiUrl + `?customerId=${id}`).then(function (response) {
      console.log(response.data);
      alert("Deleted successfully!");
      getUsers();
      // setState();
    });
  };

  const { searchText } = state;

  return (
    <div className="container-fluid">
      <div className="row">
        <div className="col-lg-6 col-md-6 col-sm-12">
          <h3>Number of movies list {state.length}</h3>
        </div>
        <div className="col-lg-6 col-md-6 col-sm-12">
          <div className="search-area">
            <div>
              <div className="form-group">
                <input
                  type="text"
                  width="100%"
                  className="form-control"
                  placeholder="Search movies.."
                  value={searchText}
                  onChange={(e) =>
                    setState({ ...state, searchText: e.target.value })
                  }
                />
              </div>
              <div>
                &ensp;
                <button className="btn btn-primary">Search</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="movie-view-area">
        <table width="100%" className="table">
          <thead>
            <tr className="bg-primary text-white">
              <th>Id</th>
              <th>Title</th>
              <th>Genre</th>
              <th>Stock</th>
              <th>Rate</th>
              <th>Operations</th>
            </tr>
          </thead>
          <tbody className="text-white">
            {state.map((customer, key) => (
              <tr key={key}>
                <td>{customer.customerId}</td>
                <td>{customer.name}</td>
                <td>{customer.mobileNo}</td>
                <td>{customer.email}</td>
                <td>{customer.address}</td>
                <td>
                  <Link
                    to={`/movie/${customer.customerId}/edit`}
                    className="mx-1 btn btn-sm btn-primary"
                  >
                    Edit
                  </Link>
                  <button
                    onClick={() => deleteUser(customer.customerId)}
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
    </div>
  );
}
