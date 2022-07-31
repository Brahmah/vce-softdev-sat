import "./App.css";
import {NavBar} from "./components/navBar";
import React, {useEffect, useState} from "react";
import {BrowserRouter as Router, Link, Outlet, Route, Routes,} from "react-router-dom";
import EntitiesView from "./views/entities";
import EntityView from "./views/entityDetail";
import AreasView from "./views/areas";
import LoginView from "./views/login";
import SettingsView from "./views/settings";
import DeviceTypeSettingsView from "./components/settings/deviceType"
import HomeView from "./views/home";
import $ from 'jquery';

export default function App() {
    return (
        <Router>
            <Routes>
                <Route path="/" element={<Layout/>}>
                    <Route index element={<HomeView/>}/>
                    <Route path="/SAT_BRH/login" element={<LoginView/>}/>
                    <Route path="/SAT_BRH/settings" element={<SettingsView/>}/>
                    <Route path="/SAT_BRH/settings/device_types/:id" element={<DeviceTypeSettingsView/>}/>
                    <Route path="/SAT_BRH/home" element={<HomeView/>}/>
                    <Route path="/SAT_BRH/devices" element={<EntitiesView context={'devices'}/>}/>
                    <Route path="/SAT_BRH/devices/:id" element={<EntityView context={'area'}/>}/>
                    <Route path="/SAT_BRH/areas" element={<AreasView/>}/>
                    <Route path="/SAT_BRH/areas/:id" element={<EntitiesView context={'area'}/>}/>
                    <Route path="*" element={<NoMatch/>}/>
                </Route>
            </Routes>
        </Router>
    );
}

function Layout() {
    const [isAuthenticated, setIsAuthenticated] = useState(true)

    useEffect(() => {
        $.ajax({
            type: 'GET',
            url: '/SAT_BRH/API/me',
            dataType: 'json',
            success: function(response) {
                setIsAuthenticated(true)
            },
            error: function(response) {
                setIsAuthenticated(false)
            }
        })
    }, [])

    if (isAuthenticated) {
        return (
            <div>
                <NavBar/>
                <main className="main-content" id="main-content">
                    <div className="mdc-drawer-app-content mdc-top-app-bar--fixed-adjust">
                        {/* An <Outlet> renders whatever child route is currently active,
          so you can think about this <Outlet> as a placeholder for
          the child routes we defined above. */}
                        <Outlet/>
                    </div>
                </main>
            </div>
        );
    } else {
        return (
            <LoginView/>
        );
    }
}

function NoMatch() {
    return (
        <div>
            <h2>Nothing to see here!</h2>
            <p>
                <Link to="/home">Go to the home page</Link>
            </p>
        </div>
    );
}
