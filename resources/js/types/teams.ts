export type Team = {
    id: string;
    name: string;
    role: string;
};

export type TeamMember = {
    id: number;
    name: string;
    email: string;
    role: string;
};

export type TeamInvitation = {
    id: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
    invited_by: {
        name: string;
    };
    created_at: string;
};

export type PendingInvitation = {
    id: string;
    team: {
        id: string;
        name: string;
    };
    invited_by: {
        name: string;
    };
    created_at: string;
};

export type Standup = {
    id: string;
    date: string;
};

export type StandupNote = {
    id: string;
    body: string;
    user: {
        id: number;
        name: string;
    };
    created_at: string;
};
