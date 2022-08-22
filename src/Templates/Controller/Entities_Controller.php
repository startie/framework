class Entities_Controller
{
#
#
# PAGES
#
#

public static function index()
{
Procedure::inc('Entities/pages/List');
}

public static function show()
{
Procedure::inc('Entities/pages/Show');
}

public static function add()
{
Procedure::inc('Entities/pages/Add');
}

public static function edit()
{
Procedure::inc('Entities/pages/Edit');
}

#
#
# ACTIONS
#
#

public static function create()
{
Procedure::inc('Entities/pages/Create');
}

public static function update()
{
Procedure::inc('Entities/pages/Update');
}

public static function hide()
{
Procedure::inc('Entities/pages/Hide');
}

public static function delete()
{
Procedure::inc('Entities/pages/Delete');
}

#
#
# TASKS
#
#

#
#
# API
#
#
}