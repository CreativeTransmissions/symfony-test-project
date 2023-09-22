# Synfony VAT Calculator Test Project
## Analysis

### Assumptions

1. This is a B2B app for fast calculation of transaction taxes.
2. The VAT rate is not something that a normal end user of the app has control over as it is a legal requirement set by the government.
3. The end user will not have the authority to change the VAT rate.
4. The system administrator will occasionally be required to update the VAT rate depending on government tax policy.
5. The end user should be given the ability to choose if a transaction includes tax or not.

---

### Notes

- We should store the VAT rate and the effective date from which it applies in a separate table and choose the correct rate based on the date on the transaction timestamp.
- This will make entering transactions simpler for the end user as the VAT rate is chosen automatically.
- A system admin will be able to add a new VAT rate as required using a separate view.
- In a real app the VAT Rate would be authenticated, however as this is only a demo authentication is out of scope

### UI

- UI is optional for this project so so save time but make the project easy to demo I have used the symfony CRUD with minimal bootstrap styling and a little JavaScript for modal confirmation windows.

### Development Process

- Planned Database schema
- Used doctrine CLI to generate entities and migrations
- Used Symfony CRUD generator to create Twig views
- Adjusted Twig views to remove elements that were not required
- Added logic to controllers
- Created the calculation methods as a VatCalculatorService for potential future reuse and to organize test
- Added happy path tests for calculation class to demo PHP Unit testing
- Added contraints to entities to prevent invalid or dangerous input
- Added bootstrap to improve UI appearence
- Added additional logic to prevent errors when deleting records
- Export CSV method added as part of TransactionController as the export datasource is the same as the index 
- Confirmation pop ups used for clear table method

---

## Database Schema

### Transactions Table

- `id`: INT, Auto Increment
- `vat_rate`: DECIMAL
- `vat_amount_ex_vat`: DECIMAL
- `amount_ex_vat`: DECIMAL
- `vat_amount_inc_vat`: DECIMAL
- `amount_inc_vat`: DECIMAL
- `vat_rate_id`: INT, Foreign Key (References `Tax Rates` table)
- `createdAt`: DATETIME, Default Current Timestamp

---

### Tax Rates Table

- `ID`: INT
- `Effective Date`: DATETIME (Rate applies from)
- `VAT Rate`: DECIMAL (Percentage as Decimal)
- `Timestamp`: DATETIME, Default Current Timestamp

---

## Setup Process
- Pull main branch of GitHub Repo
- Install dependencies
- Run Migrations
- Visit Website root.
- As the database is empty initially you will be prompted to add a VAT rate first with an effective date earlier than the current date so that all information is available for the calculation.

## Use of AI In This Project

While code generation was not used for this project outside of the symfony CLI tools, I used ChatGPT to quickly provide me with information specific to Symfony and it's configuration files as it is a framework I had not used before.

I also had to update my dev environment to PHP 8 and used ChatGPT to resolve some configuration errors related to different versions of software components.

This was a faster method than manually looking throught the symfony docs which do not have a working search engine.

Examples:

- What are the cli commands for generating migrations?
- Convert this SQL statement into Doctrine format.
- How to configure TransactionController as the website route?
- How to specify validation restrictions in symfony entities?
- How to show flash messages in twig



