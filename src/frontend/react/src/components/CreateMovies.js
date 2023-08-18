import { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import config from "../config";

const CreateMovie = () => {
  const navigate = useNavigate();
  const apiUrl = config.apiUrl;
  const [inputs, setInputs] = useState([]);

  const handleChange = (event) => {
    const name = event.target.name;
    const value = event.target.value;
    setInputs((values) => ({ ...values, [name]: value }));
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    axios
      .post(apiUrl, inputs)
      // .post("http://192.168.63.189/api/customers", inputs)
      .then(function (response) {
        // console.log(response.data.response.data);
        navigate("/");
        alert("Data submitted successfully!");
      })
      .catch((error) => {
        console.log("error", error, error.response);
        if (!error.response || error.response.status === 500)
          return alert("An unexpected error occurred!");

        const message = error.response.data.response.data;
        alert(JSON.stringify(message));
      });
  };

  return (
    <div>
      <div class="edit-area-view">
        <div>
          <form onSubmit={handleSubmit}>
            <h3 className="text-center">Create Movie</h3>
            <table cellSpacing="10">
              <tbody>
                <tr>
                  <th>
                    <label>Title: </label>
                  </th>
                  <td>
                    <input
                      className="form-control"
                      type="text"
                      placeholder="Enter title"
                      name="name"
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
                      className="form-control"
                      type="text"
                      name="mobileNo"
                      placeholder="Enter genre"
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
                      className="form-control"
                      type="text"
                      placeholder="Enter number"
                      name="email"
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
                      className="form-control"
                      type="text"
                      placeholder="Enter rate"
                      name="address"
                      onChange={handleChange}
                    />
                  </td>
                </tr>
                <tr>
                  <td colSpan="2" align="right">
                    <button width="100%" className="btn btn-sm btn-primary">
                      Save
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>
      </div>
    </div>
  );
};

export default CreateMovie;
