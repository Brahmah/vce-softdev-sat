import React from "react";
import {
  Link,
  useLocation,
} from "react-router-dom";

export class NavBar extends React.Component {
  render() {
    return (
      <aside className="mdc-drawer mdc-drawer--dismissible mdc-drawer--open">
        <div className="mdc-drawer__header">
          <h3 className="mdc-drawer__title">Devices Dashboard</h3>
          <h6 className="mdc-drawer__subtitle">Test</h6>
        </div>
        <div className="mdc-drawer__content">
          <div className="mdc-list">
            <NavBarItem to="/home" name={"Home"} icon={"home"} />
            <NavBarItem to="/areas" name={"Areas"} icon={"room"} />
            <NavBarItem
              to="/VSV/SAT/devices"
              name={"Devices"}
              icon={"settings_ethernet"}
            />
            {/* Bottom Navigation */}
            <a
              className="mdc-list-item bottomTabsForNav"
              href="../API/logout.php"
            >
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
}

export function NavBarItem({ name, icon, to }) {
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
