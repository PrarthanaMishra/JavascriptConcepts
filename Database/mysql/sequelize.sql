https://gist.github.com/zcaceres/83b554ee08726a734088d90d455bc566



Album.findAll({
  include: [
    {
      model: Artist,
      as: 'Singer',
    },
    {
      model: Genre
      through: { attributes: [] }
    }
]

include means join. Above code means join with Genre means fetch all records that in albumsgenre mapping if you want specific attributes write down it in attributes array.


Album.findAll({
  include: [
    {
      model: Artist,
      as: 'Singer',
      where: { artist.name: null }
    }
]

How do I select albums with no artists? I believe it'll simply be a case of checking that a known field in the Artist model is null

https://www.thelacunablog.com/5-things-learned-sequelize.html

Users.update(req.body, {
             where: {id: req.user.id
             },
	     individualHooks: true
      })


Issues.findAll({
 
		group: ['Issues.id'],
 
		include: [{
				model: Users,
				as: 'Vote',
				attributes: [
					[app.db.sequelize.fn('COUNT', app.db.sequelize.col('Vote.id')), 'count']
				],
				through: {
					attributes: []
				},
				duplicating: false
			}, {
				model: Users,
				attributes: ["firstName", "lastName", "user_photo"],
				duplicating: false
			}, {
				model: Categories,
				attributes: ['title'],
				duplicating: false
			},
 
		],
 
 
		where: criteria,
		order: [['updated_at', 'DESC']],
		limit: 10
 
	})




Sequelize is a Node package that allows the developer to interact with a variety of SQL databases using a single API
Indicate a database dialect in your configuration file, and Sequelize takes care of the rest.
https://lorenstewart.me/2016/09/12/sequelize-table-associations-joins/


There are two aspects of joining tables in Sequelize:

Declaring associations as part of configuration.
Declaring includes (and nested includes, if necessary) while performing actions using Sequel models.


