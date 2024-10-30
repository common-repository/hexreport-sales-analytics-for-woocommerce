import React, { createContext, useState, useContext } from 'react';

const SidebarContext = createContext();

export const useSidebar = () => {
    return useContext(SidebarContext);
}

export const SidebarProvider = ({children}) => {
    const [isSidebarActive, setIsSidebarActive] = useState(true);
    const toggleSidebar = () => {
        setIsSidebarActive(prev => !prev);
    };

    const state = { isSidebarActive, toggleSidebar };

    return (
        <SidebarContext.Provider value={state}>
            {children}
        </SidebarContext.Provider>
    );
};
