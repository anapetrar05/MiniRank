# MiniRank Agent Rules 

## General 
- Always explain every change you make, what was changed,which requirement it addresses.
- Implement the project requirement-by-requirement, in a logical order, so each step can be tested and committed independently.
- Do not make unrelated changes or introduce functionality that is not required.

## Tehnology 
- Use only the technologies specified in the project requirements:
- PHP 8+
- SQLite
- HTML
- CSS
- PDO for SQLite
- Avoid unnecessary dependencies, frameworks, libraries, or build tools.

## Requirements
- Implement the requirements incrementally rather than generating the whole application in one step.
- Before implementing a feature, identify which requirement it satisfies.

## Ranking Data
- Ranking data is completely simulated.
- Never use external search engine API.

## Architecture
- Keep database access, business logic, and presentation separated where practical.
- Use PDO for all SQLite database operations.

## Security
- Always use parameterized SQL queries.
- Never concatenate user input directly into SQL queries.
- Escape dynamic output before rendering it in HTML.

## Frontend
- Keep the UI simple and responsive.
- Use semantic HTML where practical.
- The "Refresh positions" functionality must update the relevant UI through AJAX without a full page reload.

## Database
- Use SQLite as required.
- Keep the schema minimal and understandable.
 
## Git 
- Git after every requiement that was implemented i will test and write the commit
 
## Testing 
- After each implementation step, explain exactly how to test the change manually.