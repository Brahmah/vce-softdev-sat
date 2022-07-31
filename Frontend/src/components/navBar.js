/**
 * This component renders the left-side navigation bar.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect, useState} from "react";
import {Link, useLocation,} from "react-router-dom";
import $ from "jquery";

export function NavBar() {

    const [me, setMe] = useState({
        role: "loading...",
        username: "me",
    })

    useEffect(() => {
        $.get('/SAT_BRH/API/me', function (response) {
            if (response.success) {
                setMe(response)
            }
        })
    }, [])

    function logout() {
        $.post("/SAT_BRH/API/logout", function (data, status) {
            window.location.href = "/SAT_BRH/login";
        });
    }

    return (
        <aside className="mdc-drawer mdc-drawer--dismissible mdc-drawer--open">
            <div className="mdc-drawer__header">
                <h3 className="mdc-drawer__title">Devices Dashboard</h3>
                <h6 className="mdc-drawer__subtitle">{me.username + ' | ' + me.role}</h6>
            </div>
            <div className="mdc-drawer__content">
                <div className="mdc-list">
                    <NavBarItem to="/SAT_BRH/home" name={"Home"} icon={"home"}/>
                    <NavBarItem to="/SAT_BRH/areas" name={"Areas"} icon={"room"}/>
                    <NavBarItem to="/SAT_BRH/devices" name={"Devices"} icon={"settings_ethernet"}/>
                    <NavBarItem to="/SAT_BRH/settings" name={"Settings"} icon={"settings_suggest"}/>
                    {/* Bottom Navigation */}
                    <a className="mdc-list-item bottomTabsForNav" onClick={logout}>
                        <i
                            className="material-icons mdc-list-item__graphic"
                            aria-hidden="true"
                        >
                            logout
                        </i>
                        <span className="mdc-list-item__text">Logout</span>
                    </a>
                </div>
            </div>
        </aside>
    );
}


export function NavBarItem({name, icon, to}) {
    const location = useLocation();
    const match = location.pathname.includes(to);
    const classNames =
        "mdc-list-item" + (match ? " mdc-list-item--activated" : "");

    return (
        <Link className={classNames} to={to}>
            <i className="material-icons mdc-list-item__graphic" aria-hidden="true">
                {icon}
            </i>
            <span className="mdc-list-item__text"> {name} </span>
        </Link>
    );
}
