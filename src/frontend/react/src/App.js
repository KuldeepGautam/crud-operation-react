import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import "./App.css";
import EditMovies from "./components/EditMovies";
import CreateMovie from "./components/CreateMovies";
import MovieList from "./components/MovieList";

function App() {
  return (
    <>
      <div className="home-banner-img">
        <div className="container App text-white">
          <br />
          <h2 className="text-center">
            Unlimited
            <span className="text-danger">
              <b>Movies</b>
            </span>
            , TV shows and more!
          </h2>
          <BrowserRouter>
            <div class="text-center">
              <nav>
                <ul>
                  <li>
                    <Link className="btn btn btn-warning text-white" to="/">
                      Movies List
                    </Link>
                  </li>
                  <li>
                    <Link className="btn btn btn-primary" to="movie/create">
                      Create New Movies
                    </Link>
                  </li>
                  <li>
                    <Link className="btn btn btn-danger" to="user/create">
                      Login
                    </Link>
                  </li>
                </ul>
              </nav>
            </div>
            <Routes>
              <Route path="/*" element={<MovieList />} />
              <Route path="movie/create" element={<CreateMovie />} />
              <Route path="movie/:id/edit" element={<EditMovies />} />
            </Routes>
          </BrowserRouter>
        </div>
      </div>
    </>
  );
}

export default App;
