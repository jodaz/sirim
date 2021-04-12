const knex = require('knex');

const insertMovementsQuery = `
  INSERT INTO movements
    (amount, liquidation_id, payment_id, concept_id, year_id, created_at, updated_at, deleted_at)
    SELECT
        liquidations.amount,
        liquidations.id AS liquidation_id,
        payment_id,
        concept_id,
        3,
        payment_liquidation.created_at,
        payment_liquidation.updated_at,
        liquidations.deleted_at
      FROM liquidations
      JOIN payment_liquidation
        ON liquidations.id = payment_liquidation.liquidation_id
`;

const updateYearsOfEconomicActivityMovements = (id, year) => (`
  UPDATE movements
  SET year_id = ${id}
  FROM (
    SELECT id FROM liquidations WHERE object_payment ILIKE '%${year}%'
  ) AS subquery
  WHERE movements.liquidation_id = subquery.id;
`);

const setYearId = (id, year) => (`
  UPDATE movements
  SET year_id = ${id}
  FROM (
    SELECT id
    FROM liquidations
    WHERE
      DATE_PART('year', created_at::date) = ${year}
      AND liquidation_type_id != 3
  ) AS subquery
  WHERE movements.liquidation_id = subquery.id;
`);

async function main() {
  const db = knex(require("../knexfile"));

  try {
    /**
     * Rename tables
    */
    await db.schema.createTable('movements', (table) => {
      table.increments();
      table.decimal('amount', 15, 2);
      table.integer('liquidation_id').unsigned();
      table.integer('concept_id').unsigned();
      table.integer('payment_id').unsigned();
      table.integer('year_id').unsigned();
      table.foreign('liquidation_id').references('liquidations.id');
      table.foreign('concept_id').references('concepts.id');
      table.foreign('payment_id').references('payments.id');
      table.foreign('year_id').references('years.id');
      table.timestamps();
      table.timestamp('deleted_at').nullable();
    });

    await db.schema.raw(insertMovementsQuery);
    await db.schema.raw(updateYearsOfEconomicActivityMovements('1', '2020'));
    await db.schema.raw(updateYearsOfEconomicActivityMovements('2', '2019'));
    await db.schema.raw(updateYearsOfEconomicActivityMovements('3', '2021'));
    await db.schema.raw(setYearId('3', '2021'));
    await db.schema.raw(setYearId('1', '2020'));
  } finally {
    await db.destroy();
  }
}

if (!module.parent) {
  main().catch((err) => {
    console.error(err);
    process.exit(1);
  });
}

module.exports = main;
