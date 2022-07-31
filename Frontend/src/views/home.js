/**
 * This renders the home tab
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useState, useEffect} from "react";
import {HomeDetail} from "../components/homeDetail";
import UptimeChart from "../components/uptimeChart";
import {ActivityList} from "../components/activityList";

export default function HomeView() {

    return (
        <div className={'home-container'}>
            {/*Header Bar */}
            <div className="areas-header">
                <span className="header networkingDeviceList">
                  <span>
                    <span>{'Home'}</span>
                  </span>
                </span>
            </div>
            {/*Main Content*/}
            <section className={'home-leading-container'}>
                <UptimeChart/>
                <h5><span>Alerts</span></h5>
                <ActivityList context={'alerts'} itemId={'*'}/>
            </section>
            <section className={'areas-detail'}>
                <HomeDetail/>
            </section>
        </div>
    );
}
