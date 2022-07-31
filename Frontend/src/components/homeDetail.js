/**
 * This renders the right-detail view on the home tab.
 * The detail view is similar to the area detail but missing options.
 * Data is fetched from the same API endpoint as the area detail but the
 * requested area id is the root area id (0), so the API returns the appropriate
 * data for the root area. This is an extension of the standard data but with more detail
 * as to not disrupt any API models that depend on the data structure.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect} from "react";
import $ from "jquery";
import {ActivityList} from "./activityList";

// MARK: - Home Detail
export function HomeDetail({}) {
    const defaultDevicesInfo = {
        totalEntities: '...',
        onlineEntities: '...',
        offlineEntities: '...',
    };
    const [devicesInfo, setDevicesInfo] = React.useState(defaultDevicesInfo);

    function getItemInfo() {
        setDevicesInfo(defaultDevicesInfo);
        $.get(`/SAT_BRH/API/areas/0/info`, function (data) {
            setDevicesInfo(data);
        });
    }

    useEffect(() => {
        getItemInfo();
    }, []);

    return (
        <div style={{marginRight: '23px'}}>
            <h2>Overview</h2>
            <h3 style={{color: 'gray', fontSize: '14px', fontWeight: 200, marginTop: '-14px'}}>All Devices</h3>
            <div className="island">
                <h2 className="subheader">Summary</h2>

                <div className="content">
                    <div className="summary-item">
                        <p className="infographic">
                            <span className="title" style={{'color': '#008000'}}>Online Devices</span>
                            <span className="value">{devicesInfo.onlineEntities}</span>
                        </p>
                    </div>
                    <div className="summary-item">
                        <p className="infographic">
                            <span className="title" style={{color: '#ff0000'}}>Offline Devices</span>
                            <span className="value">{devicesInfo.offlineEntities}</span>
                        </p>
                    </div>
                    <div className="summary-item">
                        <p className="infographic">
                            <span className="title" style={{color: '#1b4a6b'}}>Total</span>
                            <span className="value">{devicesInfo.totalEntities}</span>
                        </p>
                    </div>
                </div>

            </div>

            <div className="island">
                <h2 className="subheader" style={{opacity: 0.1}}>`</h2>
                <h2 className="subheader">Recent Activity</h2>

                <div className="content">
                    <ActivityList context={'all'} itemId={'*'}/>
                </div>
            </div>

        </div>
    );
}
