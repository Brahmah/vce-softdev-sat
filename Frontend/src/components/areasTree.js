/**
 * This component is a tree builder that builds a tree from a hierarchical list of areas.
 * This uses useImperativeHandle to allow for parent components to access the tree refresh
 * lifecycle.
 *
 * It is dependent on a component authored by @bartaxyz on GitHub.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect, useState, forwardRef, useImperativeHandle} from "react";
import { ReactTreeList } from "@bartaxyz/react-tree-list";
import $ from "jquery";

export const AreasTree = forwardRef((props, ref) => {
    useImperativeHandle(ref, () => ({
        reloadAreaTree() {
            refreshTree();
        }
    }));

    const [selectedId, setSelectedId] = useState("");

    const [data, setTreeData] = useState([]);
    const [canDrag, setCanDrag] = useState(false);

    const onDrop = (dragingNode, dragNode, drogType) => {
        console.log("dragingNode:", dragingNode);
        console.log("dragNode:", dragNode);
        console.log("drogType:", drogType);
    };

    function refreshTree() {
        $.get(
            `/SAT_BRH/API/areas/0/nodes?expand`,
            (area) => {
                const areaNodeStatesString = sessionStorage.getItem('areaNodeStates')
                if (areaNodeStatesString) {
                    const areaNodeStates = JSON.parse(areaNodeStatesString);
                    function rememberOpenState(children) {
                        children.forEach(function(child) {
                            if (child.children) {
                                rememberOpenState(child.children)
                            }
                            if (child.type !== 'entity') {
                                child.open = areaNodeStates[child.id] || child.open;
                            }
                        })
                    }
                    rememberOpenState(area.children)
                }
                // set area tree
                setTreeData(area.children);
                // remember selectedAreaId
                if (sessionStorage.getItem('selectedAreaId') !== null) {
                    setSelectedId(sessionStorage.getItem('selectedAreaId'));
                } else if (area.children.length > 0) {
                    setSelectedId(area.children[0].id);
                }
            }
        );
    }

    function onTreeChange(newTree) {
        setTreeData(newTree);
        // oh cool, something changed. let's figure out what nodes are open and closed.
        const nodeStates = {};
        diffTreeNode(newTree);
        function diffTreeNode(children) {
            children.forEach(function(child) {
                if (child.children) {
                    diffTreeNode(child.children)
                }
                if (child.type !== 'entity') {
                    nodeStates[child.id] = child.open;
                }
            })
        }
        sessionStorage.setItem('areaNodeStates', JSON.stringify(nodeStates))
    }

    useEffect(() => {
        refreshTree()
    }, [])

    return (
        <ReactTreeList
            draggable={canDrag}
            data={data}
            selectedId={selectedId}
            onDrop={onDrop}
            onSelected={(item) => {
                console.log("selected item:", item);
                setSelectedId(item.id ?? undefined);
                props.onItemSelect(item);
                sessionStorage.setItem('selectedAreaId', item.id);
            }}
            onChange={onTreeChange}
            itemDefaults={{ open: false, arrow: "▸", icon: "" }}
            itemOptions={{
                focusedOutlineColor: "rgba(27,74,107,0.16)",
                focusedOutlineWidth: 1,
                focusedBorderRadius: 0,
                focusedBackgroundColor: "rgba(27,74,107,0.19)",
            }}
        />
    );
});
