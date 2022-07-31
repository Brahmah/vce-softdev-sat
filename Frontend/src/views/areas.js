/**
 * This renders the area tab
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useRef} from "react";
import {AreasTree} from "../components/areasTree";
import {AreasDetail} from "../components/areaDetail";
import {ToastContainer} from "react-toastify";

export default function AreasView() {
    const [selectedItem, selectItem] = React.useState(null);
    const childRef = useRef(null);

    function reloadAreaTree() {
        childRef.current.reloadAreaTree()
    }

    return (
        <div>
            {/* Header Bar */}
            <ToastContainer/>
            <div className="areas-header">
              <span className="header networkingDeviceList">
                <span>
                  <span>Areas</span>
                  {/*<span className="header-badge">Testing Hierarchy instead</span>*/}
                </span>
              </span>
            </div>
            {/* Areas Tree */}
            <section className={'areas-tree'}>
                <AreasTree onItemSelect={selectItem} ref={childRef}/>
            </section>
            {/* Area Details */}
            <section className={'areas-detail'}>
                <AreasDetail selectedItem={selectedItem} reloadAreaTree={reloadAreaTree}/>
            </section>
        </div>
    )
}
