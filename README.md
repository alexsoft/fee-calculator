Fee Calculation
===============

## Background

This test is designed to evaluate your problem-solving approach and engineering ability. Demonstrate your knowledge
of OOP concepts, SOLID principles, design patterns, domain-driven design and clean and extensible architecture.

Main interface is a single console script entrypoint.
The `bin/calculate-fee` command **SHOULD** only be used for bootstrapping and running your
solution, therefore is expected most of your code will live in the `src` and `tests` folders.

## The task

Build a fee calculator that given a monetary **amount** and a **term** (the contractual duration of the loan,
expressed as a number of months) will produce an appropriate **fee** for a loan based on a fee structure
and a set of rules described below.

It is a CLI tool: `bin/calculate-fee`. It takes the mentioned **amount** and **term** as the only arguments
and in that order. (e.g. `bin/calculate-fee 20,000.00 24`).

Upon success, the script **MUST** print the resulting **fee** to `stdout` followed by a line feed (`\n`) and
exit with status code `zero`. The fee is formatted numerically, with two decimal places and with no currency
identifiers or symbols (e.g. `1,223.44`). Supporting different currencies is not required
as only monetary amounts matter.

Upon failure, the script must print any errors to `stderr` and exit with `non-zero` exit code.

Business logic is following:

- The fee structure does not follow a formula.
- Values in between the breakpoints should be interpolated linearly between the lower bound and upper bound that they fall between.
- The number of breakpoints, their values, or storage might change.
- The term can be either 12 or 24 (the number of months). You can also assume values will always be within this set.
- The fee should be rounded up such that the sum of the fee and the loan amount is exactly divisible by €5.
- The minimum amount for a loan is €1,000, and the maximum is €20,000.
- Values will always be within this range but **there may be any values up to 2 decimal places**.

Example inputs/outputs:

| Loan Amount (in EUR) | Term (in Months) | Fee (in EUR) |
|----------------------|------------------|--------------|
| 11,500.00            | 24               | 460.00       |
| 19,250.00            | 12               | 385.00       |

# Fee Structure

The fee structure doesn't follow particular algorithm, and it is possible that same fee will be applicable for different
amounts.

### Term 12 Breakpoints

| Amount | Fee |
|--------|-----|
| 1,000  | 50  |
| 2,000  | 90  |
| 3,000  | 90  |
| 4,000  | 115 |
| 5,000  | 100 |
| 6,000  | 120 |
| 7,000  | 140 |
| 8,000  | 160 |
| 9,000  | 180 |
| 10,000 | 200 |
| 11,000 | 220 |
| 12,000 | 240 |
| 13,000 | 260 |
| 14,000 | 280 |
| 15,000 | 300 |
| 16,000 | 320 |
| 17,000 | 340 |
| 18,000 | 360 |
| 19,000 | 380 |
| 20,000 | 400 |

### Term 24 Breakpoints

| Amount | Fee |
|--------|-----|
| 1,000  | 70  |
| 2,000  | 100 |
| 3,000  | 120 |
| 4,000  | 160 |
| 5,000  | 200 |
| 6,000  | 240 |
| 7,000  | 280 |
| 8,000  | 320 |
| 9,000  | 360 |
| 10,000 | 400 |
| 11,000 | 440 |
| 12,000 | 480 |
| 13,000 | 520 |
| 14,000 | 560 |
| 15,000 | 600 |
| 16,000 | 640 |
| 17,000 | 680 |
| 18,000 | 720 |
| 19,000 | 760 |
| 20,000 | 800 |
