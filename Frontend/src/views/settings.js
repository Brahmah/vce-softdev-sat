/**
 * This view renders the settings tab.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React from "react";
import DeviceTypesSettingsView from "../components/settings/deviceTypes";
import UsersSettingsView from "../components/settings/users";

export default function SettingsView() {
    return (
        <div>
            {/*Header Bar */}
            <div className="areas-header">
                <span className="header networkingDeviceList">
                  <span>
                    <span>{'Settings'}</span>
                    <span className="header-badge">{'Admin'}</span>
                  </span>
                </span>
            </div>
            {/*Main Content*/}
            <section className="settings">
                <div className="settings-container">
                    <div className="settings-body">
                        <UsersSettingsView/>
                        <DeviceTypesSettingsView/>
                    </div>
                </div>
            </section>
        </div>
    );
}
