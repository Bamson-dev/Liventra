import React from 'react';

export interface LiventraComponentProps {
    webinarId?: string | number;
    className?: string;
    style?: React.CSSProperties;
    onRegister?: (data: any) => void;
    onOfferClick?: (offer: any) => void;
}

export const LiventraRegistration: React.FC<LiventraComponentProps> = ({ webinarId = '1', className, style, onRegister }) => {
    return (
        <div className={`liventra-react-registration ${className || ''}`} style={style}>
            <div data-liventra-embed="registration" data-webinar-id={String(webinarId)}></div>
        </div>
    );
};

export const LiventraLiveRoom: React.FC<LiventraComponentProps> = ({ webinarId = '1', className, style }) => {
    return (
        <div className={`liventra-react-liveroom ${className || ''}`} style={style}>
            <div data-liventra-embed="live" data-webinar-id={String(webinarId)}></div>
        </div>
    );
};

export const LiventraReplay: React.FC<LiventraComponentProps> = ({ webinarId = '1', className, style }) => {
    return (
        <div className={`liventra-react-replay ${className || ''}`} style={style}>
            <div data-liventra-embed="replay" data-webinar-id={String(webinarId)}></div>
        </div>
    );
};

export const LiventraCheckout: React.FC<LiventraComponentProps> = ({ webinarId = '1', className, style }) => {
    return (
        <div className={`liventra-react-checkout ${className || ''}`} style={style}>
            <div data-liventra-embed="checkout" data-webinar-id={String(webinarId)}></div>
        </div>
    );
};
