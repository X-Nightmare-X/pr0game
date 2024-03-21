using HTTP
using Sockets
using JSON
using LinearAlgebra
using Dates


mutable struct Stats_current
    amount::Int
    attack::Float64
    hull::Float64
    shield::Float64
    function Stats_current()
        new(0,0,0,0)
    end
    function Stats_current(a,b,c,d)
        new(a,b,c,d)
    end
end

mutable struct Player
    damage::Float64
    totaldamage::Float64
    shieldabsorb::Float64
    totalshieldabsorb::Float64
    id::Int
    destroyed_ships::Dict{Int,Int}
    has_ships::Dict{Int,Stats_current}
    function Player(id,shipdict)
        new(0.,0.,0.,0.,id,shipdict,Dict{Int,Stats_current}())
    end
end

mutable struct Ship
    shield::Float64
    maxshield::Float64
    attack::Float64
    hull::Float64
    maxhull::Float64
    id::Int
    player::Player
    function Ship(attack::Float64, maxshield::Float64, maxhull::Float64,id::Int,player::Player)
        new(maxshield,maxshield, attack,maxhull, maxhull,id,player)
    end
end



const ship_destroy = Dict{Int,Bool}()

const shipinfos = Dict{Int,Tuple{Int,Int,Int}}()

const rapidfiredict = Dict{Int, Dict{Int, Int}}()


function load_ship_information()
    println("loading data....")
    empty!(ship_destroy)
    empty!(shipinfos)
    empty!(rapidfiredict)
    file = open(ARGS[1], "r")
    for line in eachline(file)
        id,attack,shield,hull,destroy,rapidfire=split(line, ";")
        id=parse(Int64,id)
        attack=parse(Int64,attack)
        shield=parse(Int64,shield)
        hull=parse(Int64,hull)
        destroy=parse(Bool,destroy)
        rapidfire=strip(rapidfire)
        shipinfos[id]=(attack,shield,hull)
        ship_destroy[id]=destroy
        rapidfiredict[id]=Dict{Int, Int}()
        if length(rapidfire)==0
            continue
        end
        rfentries=split(rapidfire,'-')
        for rfe in rfentries
            shipid,amount=split(rfe,',')
            rapidfiredict[id][parse(Int64,shipid)]=parse(Int64,amount)
        end
    end
end

function calculate_shoot!(attackers::Vector{Ship}, defenders::Vector{Ship})
            for ship in attackers
                shoot_at!(ship,defenders)
            end
            for ship in defenders
                 shoot_at!(ship,attackers)
            end
end

function shoot_at!(ship::Ship,defenders::Vector{Ship})
    shiptarget=rand(defenders)
    if  ship.attack > shiptarget.shield * 0.01# bounce check
        if shiptarget.hull >0
            #attackshot
            penetration = ship.attack - shiptarget.shield
            if penetration >= 0
                ship.player.shieldabsorb += shiptarget.shield
                damagedone =min(penetration,shiptarget.hull)
                ship.player.damage +=damagedone
                shiptarget.player.has_ships[shiptarget.id].hull -=damagedone
                shiptarget.shield=0
                shiptarget.hull -=penetration
            else
                ship.player.shieldabsorb +=ship.attack
                shiptarget.shield -= ship.attack
            end
            #explosion for ships
            if   ship_destroy[shiptarget.id] && shiptarget.hull < 0.7 * shiptarget.maxhull
            #if floor(shiptarget.id /100)==2 && shiptarget.hull < 0.7 * shiptarget.maxhull
                    if rand(0:shiptarget.maxhull) > shiptarget.hull
                        shiptarget.player.has_ships[shiptarget.id].hull -=shiptarget.hull
                        shiptarget.hull=0
                    end
            end
            if shiptarget.hull <=0
                shiptarget.player.has_ships[shiptarget.id].amount -=1
                shiptarget.player.has_ships[shiptarget.id].shield -=shiptarget.maxshield
                shiptarget.player.has_ships[shiptarget.id].attack -=shiptarget.attack
                ship.player.destroyed_ships[shiptarget.id] +=1
            end
        end
        if  haskey(rapidfiredict[ship.id],shiptarget.id)
            if rand(1:rapidfiredict[ship.id][shiptarget.id]) != rapidfiredict[ship.id][shiptarget.id]
                shoot_at!(ship,defenders)
            end
        end

    else

    end
end

function generate_ships(hasships::Dict{Int,Int},weapontech::Int,shieldtech::Int,armortech::Int,player::Player)
    ships= Ship[]
    for (shipid,amount) in sort(collect(pairs(hasships)))
        #println(shipid)
        attack,shield,armor = shipinfos[shipid]
        armor=armor * (1+armortech*0.1)
        shield=shield * (1+shieldtech*0.1)
        attack=attack * (1+weapontech*0.1)
        player.has_ships[shipid]=Stats_current(amount,attack*amount,armor*amount,shield*amount)
        for i in 1:amount
            push!(ships,Ship(attack,shield,armor,shipid,player))
        end
    end
    return ships
end

function simulate_attack(attackers::Vector{Ship}, defenders::Vector{Ship},attacknames::Vector{Player},defnames::Vector{Player}, rounds::Int)
    totalstart = now()
    shootingtime=0
    calculate_losses=0
    calculate_current=0



    loss_report=Tuple{Dict{String,Int},Dict{String,Int}}[]
    have_report=Tuple{Dict{Int,Dict{Int64, Stats_current}},Dict{Int,Dict{Int64, Stats_current}}}[]
    round=0

    while round < rounds && length(attackers)>0 && length(defenders) >0
        shootstart = now()
        calculate_shoot!(attackers,defenders)
        shootingtime+=Dates.value(now()-shootstart)
        start_current = now()
        attackers=getlosses(attackers)
        calculate_losses+=Dates.value(now()-start_current)
        start_current = now()
        current_ships_attacker=Dict{Int,Dict{Int64, Stats_current}}()
        for player in attacknames
            filter!(x -> x[2].amount != 0, player.has_ships)
            current_ships_attacker[player.id]=deepcopy(player.has_ships)
        end
        calculate_current+=Dates.value(now()-start_current)
        lossda=Dict{String,Int}("shielddamage"=>0,"hulldamage"=>0)
        for player in attacknames
            lossda["shielddamage"] +=floor(player.shieldabsorb)
            lossda["hulldamage"] +=floor(player.damage)
            player.totalshieldabsorb+=player.shieldabsorb
            player.totaldamage +=player.damage
            player.damage=0
            player.shieldabsorb=0
        end
        start_current = now()
        defenders =getlosses(defenders)
        calculate_losses+=Dates.value(now()-start_current)
        start_current = now()
        current_ships_defender=Dict{Int,Dict{Int64, Stats_current}}()
        for player in defnames
            filter!(x -> x[2].amount != 0, player.has_ships)
            current_ships_defender[player.id]=deepcopy(player.has_ships)
        end
        calculate_current+=Dates.value(now()-start_current)
        lossdd=Dict{String,Int}("shielddamage"=>0,"hulldamage"=>0)
        for player in defnames
            lossdd["shielddamage"] +=floor(player.shieldabsorb)
            lossdd["hulldamage"] +=floor(player.damage)
            player.totalshieldabsorb+=player.shieldabsorb
            player.totaldamage +=player.damage
            player.damage=0
            player.shieldabsorb=0
        end
        push!(loss_report,(lossda,lossdd))
        push!(have_report,(current_ships_attacker,current_ships_defender))
        round+=1
    end
    ats=length(attackers)
    dfs=length(defenders)
    totalend = now()
    println("shootingtime:",shootingtime)
    println("calculate losses:",calculate_losses)
    println("calculate current:",calculate_current)
    println("overheadtime:",Dates.value(totalend-totalstart)-shootingtime-calculate_current-calculate_losses)
    if ats == 0 && dfs > 0
          return -1,loss_report,have_report,attacknames,defnames
     end
    if dfs == 0 && ats > 0
        return 1,loss_report,have_report,attacknames,defnames
     end
    return 0,loss_report,have_report,attacknames,defnames
end

function getlosses(ships::Array{Ship})
        for ship in ships
        ship.shield=ship.maxshield
        end
        return filter((x)-> x.hull >0,ships)
end


function startsim(atdicts::Vector{Tuple{Int,Int,Int,Int,Dict{Int,Int}}},defdicts::Vector{Tuple{Int,Int,Int,Int,Dict{Int,Int}}})
    start = now()
    attackers=Vector{Ship}()
    attackersp=Vector{Player}()
    deffersp=Vector{Player}()
    for (fleetid,wtech,shieldtech,armortech,pd) in atdicts
        deleted_dict=Dict{Int,Int}()
        for (key, value) in shipinfos
            deleted_dict[key]=0
        end
        push!(attackersp,Player(fleetid,deleted_dict))
        attackers = vcat(attackers,generate_ships(pd,wtech,shieldtech,armortech,attackersp[end]))
    end
    dfers=Vector{Ship}()
    for (fleetid,wtech,shieldtech,armortech,pd) in defdicts
        deleted_dict=Dict{Int,Int}()
        for (key, value) in shipinfos
            deleted_dict[key]=0
        end
        push!(deffersp,Player(fleetid,deleted_dict))
        dfers = vcat(dfers,generate_ships(pd,wtech,shieldtech,armortech,deffersp[end]))
    end
    endinit = now()
    startsim = now()
    simres=@inbounds simulate_attack(attackers,dfers,attackersp,deffersp,6)
    endsim = now()
    println("init: ",endinit-start)
    println("sim: ",endsim-startsim)
    return simres
end
precompile(load_ship_information,())
load_ship_information()
precompile(startsim,(Vector{Tuple{Int,Int,Int,Int,Dict{Int,Int}}},Vector{Tuple{Int,Int,Int,Int,Dict{Int,Int}}}))
precompile(getlosses,(Vector{Ship},))
precompile(simulate_attack,(Vector{Ship}, Vector{Ship},Vector{Player},Vector{Player},Int))
precompile(generate_ships,(Dict{Int,Int},Int,Int,Int,Player))
precompile(shoot_at!,(Ship,Vector{Ship}))
precompile(calculate_shoot!,(Vector{Ship}, Vector{Ship}))

#println("started")

#winner,roundlists=startsim([["at1",15,15,15,Dict( 204 =>1000,206=>5000,  207=> 1000,   213=> 5000,214=>1000, 215=> 10000)]],[["at1",15,15,15,Dict( 204 =>1000,206=>5000,  207=> 1000,   213=> 500,214=>1000, 215=> 10000)]])
#println(startsim([["at1",15,15,15,Dict( 214=>250,401 =>5000,405 =>100)]],[["at1",15,15,15,Dict( 204 =>100,206 =>5000,207=>250,215=>1500,213=>100)]]))
#JSON.json(Dict("outcome"=>winner,"losses"=>roundlists))
#@time startsim([["at1",15,15,15,Dict( 214=>1000000)]],[["at1",15,15,15,Dict( 204 =>40_000_000,206 =>15000_000,207=>7000000,215=>5000000,213=>2000000)]])

#exit(0)
#

function update_ships(req::HTTP.Request)
    load_ship_information()
    HTTP.Response(200,"done")

end

function pingalive(req::HTTP.Request)
    HTTP.Response(200,"alive")

end

function postbattle(req::HTTP.Request)
        try
            # Parse the JSON payload from the request
            payload = JSON.parse(String(req.body))
            atrlist=Vector{Tuple{Int,Int,Int,Int,Dict{Int,Int}}}()
            for atd in payload["attackers"]
                fleetid=pop!(atd, "fleetid")
                wtech=pop!(atd, "wtech")
                stech=pop!(atd, "shieldtech")
                atech=pop!(atd, "armortech")
                shipdict=Dict{Int,Int}()
                for (sid,amount) in atd
                    shipdict[parse(Int64,sid)]=amount
                end
                push!(atrlist,(fleetid,wtech,stech,atech,shipdict))
            end
            deflist=Vector{Tuple{Int,Int,Int,Int,Dict{Int,Int}}}()
            for atd in payload["defenders"]
                fleetid=pop!(atd, "fleetid")
                wtech=pop!(atd, "wtech")
                stech=pop!(atd, "shieldtech")
                atech=pop!(atd, "armortech")
                shipdict=Dict{Int,Int}()
                for (sid,amount) in atd
                    shipdict[parse(Int64,sid)]=amount
                end
                push!(deflist,(fleetid,wtech,stech,atech,shipdict))
            end
            winner,loss_report,have_report,attack_players,def_players=startsim(atrlist,deflist)
            destroyedstats_attacker=Dict{Int,Dict{Int,Int}}()
            for player in attack_players
                destroyedstats_attacker[player.id]=Dict{Int,Int}()
                destroyedstats_attacker[player.id][-1]=floor(player.totalshieldabsorb)
                destroyedstats_attacker[player.id][-2]=floor(player.totaldamage)
                for (shipid,shipamount) in player.destroyed_ships
                    destroyedstats_attacker[player.id][shipid]=shipamount
                end
            end
            destroyedstats_defender=Dict{Int,Dict{Int,Int}}()
            for player in def_players
                destroyedstats_defender[player.id]=Dict{Int,Int}()
                destroyedstats_defender[player.id][-1]=floor(player.totalshieldabsorb)
                destroyedstats_defender[player.id][-2]=floor(player.totaldamage)
                for (shipid,shipamount) in player.destroyed_ships
                    destroyedstats_defender[player.id][shipid]=shipamount
                end
            end
            #TODO destroyed dict
            # Respond with a success message
            return HTTP.Response(200,JSON.json(Dict("outcome"=>winner,"round_lost"=>loss_report,"round_have"=>have_report,"destroyed_attacker"=>destroyedstats_attacker,"destroyed_defender"=>destroyedstats_defender)))
        catch err
            println(err)
            # Respond with an error message if JSON parsing fails
            return HTTP.Response(400,  "{\"error\": \"Invalid JSON payload\"}")
        end

end


function gethtml(req::HTTP.Request)
    # Extract the requested path from the URL
    path = req.target[2:end]
    # Check if the requested path is a file and exists
    if isfile(path)
        # Read the contents of the file
        content = String(read(path))

        # Return a response with the file content and appropriate headers
        return HTTP.Response(200, content)
    else
        # Return a 404 Not Found response if the file doesn't exist
        return HTTP.Response(404, "File not found")
    end
end

function main()
    router = HTTP.Router()
    HTTP.register!(router, "POST", "/battlesim", postbattle)
    HTTP.register!(router, "GET", "/updateships", update_ships)
    HTTP.register!(router, "GET", "/ping", pingalive)
    #HTTP.register!(router, "POST", "/battlesimmulti", postbattlesim)

    HTTP.register!(router, "GET", "/*", gethtml)
    server = HTTP.serve!(router, Sockets.localhost,parse(Int64, ARGS[2]))
    wait(server)
end
function julia_main()::Cint
 main()
  return 0 # if things finished successfully
end
precompile(postbattle,(HTTP.Request,))
precompile(gethtml,(HTTP.Request,))

julia_main()
