import { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate, useParams } from "react-router-dom";
import config from "../config";

export default function EditMovies() {
  const navigate = useNavigate();
  const apiUrl = config.apiUrl;
  const [inputs, setInputs] = useState({});

  const { id } = useParams();

  useEffect(() => {
    getUser();
  }, []);

  function getUser() {
    axios.get(apiUrl + `?customerId=${id}`).then(function (response) {
      console.log("response.data", response.data);
      setInputs(response.data.response.data[0]);
    });
  }

  // handleChange
  const handleChange = (event) => {
    const name = event.target.name;
    const value = event.target.value;
    setInputs((values) => ({ ...values, [name]: value }));
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    axios.put(apiUrl + `?customerId=${id}`, inputs).then(function (response) {
      console.log(response.data);
      alert("Data updated successfully....!!");
      navigate("/");
    });
  };

  return (
    <div>
      <div class="edit-area-view">
        <div>
          <form onSubmit={handleSubmit}>
            <h3 className="text-center">Edit Movie</h3>
            <table cellSpacing="10">
              <tbody>
                <tr>
                  <th>
                    <label>Title: </label>
                  </th>
                  <td>
                    <input
                      value={inputs.name}
                      type="text"
                      name="name"
                      className="form-control"
                      onChange={handleChange}
                    />
                  </td>
                </tr>
                <tr>
                  <th>
                    <label>Genre: </label>
                  </th>
                  <td>
                    <input
                      value={inputs.email}
                      type="text"
                      name="email"
                      className="form-control"
                      onChange={handleChange}
                    />
                  </td>
                </tr>
                <tr>
                  <th>
                    <label>Stock: </label>
                  </th>
                  <td>
                    <input
                      value={inputs.mobileNo}
                      type="text"
                      name="mobile"
                      className="form-control"
                      onChange={handleChange}
                    />
                  </td>
                </tr>
                <tr>
                  <th>
                    <label>Rate: </label>
                  </th>
                  <td>
                    <input
                      value={inputs.address}
                      type="text"
                      name="mobile"
                      className="form-control"
                      onChange={handleChange}
                    />
                  </td>
                </tr>
                <tr>
                  <td colSpan="2" align="right">
                    <button className="btn btn-sm btn-primary">Save</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>
      </div>
    </div>
  );
}
