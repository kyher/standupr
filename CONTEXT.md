# Domain Glossary

## Standup
A daily stand-up record tied to a specific date and team. Contains zero or more StandupNotes submitted by team members.

## StandupNote
A single update submitted by a team member for a Standup. Has a body (free text) and may be flagged as a Blocker.

## Blocker
A flag (`has_blocker: boolean`) on a StandupNote indicating the team member is blocked. Blockers are visually distinguished in the Standup view and can be filtered for. Can be set inline by typing `#blocker` (case-insensitive) in the note body — the tag is preserved in the saved text.
