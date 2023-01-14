import { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate, useParams } from "react-router-dom";

export default function ListUser() {
  const navigate = useNavigate();

  const [inputs, setInputs] = useState({});

  const { id } = useParams();

  useEffect(() => {
    getUser();
  }, []);

  function getUser() {
    // axios.get("http://192.168.184.189/api/customers").then(function (response) {
    //   console.log(response.data);
    //   setState(response.data.response.data);
    // });
    axios
      .get(`http://192.168.0.186/api/customers?customerId=${id}`)
      .then(function (response) {
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
    axios
      .put(`http://192.168.0.186/api/customers${id}/edit`, inputs)
      .then(function (response) {
        console.log(response.data);
        navigate("/");
      });
  };
  return (
    <div>
      <h1>Edit user</h1>
      <form onSubmit={handleSubmit}>
        <table cellSpacing="10">
          <tbody>
            <tr>
              <th>
                <label>Name: </label>
              </th>
              <td>
                <input
                  value={inputs.name}
                  type="text"
                  name="name"
                  onChange={handleChange}
                />
              </td>
            </tr>
            <tr>
              <th>
                <label>Email: </label>
              </th>
              <td>
                <input
                  value={inputs.email}
                  type="text"
                  name="email"
                  onChange={handleChange}
                />
              </td>
            </tr>
            <tr>
              <th>
                <label>Mobile: </label>
              </th>
              <td>
                <input
                  value={inputs.mobile}
                  type="text"
                  name="mobile"
                  onChange={handleChange}
                />
              </td>
            </tr>
            <tr>
              <th>
                <label>Address: </label>
              </th>
              <td>
                <input
                  value={inputs.address}
                  type="text"
                  name="mobile"
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
  );
}
