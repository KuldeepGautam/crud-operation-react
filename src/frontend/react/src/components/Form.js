import React, { useState } from "react";

const Form = () => {
  const [formData, setFormData] = useState([]);

  const onHandleChange = (e) => {
    // const name = e.target.value;
    setFormData(e.target.value);
  };

  const onSubmit = (e) => {
    e.preventDefault();
  };

  return (
    <>
      <div className="container">
        <div>
          <h3>Form data: {formData}</h3>
        </div>
        <form>
          <table>
            <tbody>
              <tr>
                <td>
                  <label>User Name:</label>
                </td>
                <td>
                  <input onChange={onHandleChange} type="text" name="name" />
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button
                    onSubmit={onSubmit}
                    type="submit"
                    className="btn btn-primary"
                  >
                    Submit
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
    </>
  );
};

export default Form;
