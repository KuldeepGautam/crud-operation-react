import { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import config from "../config";

const CreateCustomer = () => {
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
    <div className="container">
      <h1>Create user</h1>
      <form onSubmit={handleSubmit}>
        <table cellSpacing="10">
          <tbody>
            <tr>
              <th>
                <label>Name: </label>
              </th>
              <td>
                <input type="text" name="name" onChange={handleChange} />
              </td>
            </tr>
            <tr>
              <th>
                <label>Mobile: </label>
              </th>
              <td>
                <input type="text" name="mobileNo" onChange={handleChange} />
              </td>
            </tr>
            <tr>
              <th>
                <label>Email: </label>
              </th>
              <td>
                <input type="text" name="email" onChange={handleChange} />
              </td>
            </tr>
            <tr>
              <th>
                <label>Address: </label>
              </th>
              <td>
                <input type="text" name="address" onChange={handleChange} />
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
  );
};

export default CreateCustomer;
