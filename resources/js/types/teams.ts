export type Team = {
    id: string;
    name: string;
    role: string;
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
