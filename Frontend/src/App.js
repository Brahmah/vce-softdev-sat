import "./App.css";
import { NavBar } from "./components/navBar";
import React from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Outlet,
  Link,
} from "react-router-dom";
import EntitiesView from "./views/entities";
import EntityView from "./views/entityDetail";
import AreasView from "./views/areas";

export default function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Layout />}>
            <Route index element={<Home />} />
            <Route path="./SAT_BRH/home" element={<Home />} />
            <Route path="/SAT_BRH/devices" element={<EntitiesView context={'devices'} />} />
            <Route path="/SAT_BRH/devices/:id" element={<EntityView context={'area'} /> } />
            <Route path="/SAT_BRH/areas" element={<AreasView />} />
            <Route path="/SAT_BRH/areas/:id" element={<EntitiesView context={'area'} />} />
          <Route path="*" element={<NoMatch />} />
        </Route>
      </Routes>
    </Router>
  );
}

function Layout() {
  return (
    <div>
      <div style={{ marginTop: 20 }}>
        <NavBar />
        <main className="main-content" id="main-content">
          <div className="mdc-drawer-app-content mdc-top-app-bar--fixed-adjust">
            {/* An <Outlet> renders whatever child route is currently active,
          so you can think about this <Outlet> as a placeholder for
          the child routes we defined above. */}
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  );
}

function Home() {
  return (
    <div>
      <h2 style={{ float: "right" }}>Home</h2>
    </div>
  );
}

function Dashboard() {
  return (
    <div>
      <h2>Dashboard</h2>
    </div>
  );
}

function NoMatch() {
  return (
    <div>
      <h2>Nothing to see here!</h2>
      <p>
        <Link to="/">Go to the home page</Link>
      </p>
    </div>
  );
}
