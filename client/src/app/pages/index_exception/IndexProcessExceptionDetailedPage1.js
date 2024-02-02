import React, {useEffect, useMemo, useContext} from "react";
import {makeStyles} from '@material-ui/core/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import {withAuth} from '@okta/okta-react';
import Box from '@material-ui/core/Box';
import Button from '@material-ui/core/Button';
import FastForwardIcon from '@material-ui/icons/FastForward';
import AttachmentIcon from '@material-ui/icons/Attachment';
import ViewListIcon from '@material-ui/icons/ViewList';
import ContactsIcon from '@material-ui/icons/Contacts';
import EntityListing from '../entity/EntityListing';
import ContactList from '../entity/ContactList';
import {metronic} from "../../../_metronic";
import QuickStatsChart from "../../widgets/QuickStatsChart";
import OrderStatisticsChart from "../../widgets/OrderStatisticsChart";
import OrdersWidget from "../../widgets/OrdersWidget";
import SalesBarChart from "../../widgets/SalesBarChart";
import {useSelector} from "react-redux";
import {
    Portlet,
    PortletBody,
    PortletHeader,
    PortletHeaderToolbar
} from "../../partials/content/Portlet";
import PortletHeaderDropdown from "../../partials/content/CustomDropdowns/PortletHeaderDropdown";
import {Title} from '../home/helpers/titles';
import PermIdentityIcon from '@material-ui/icons/PermIdentity';
import clsx from 'clsx';
import TextField from '@material-ui/core/TextField';
import FormGroup from '@material-ui/core/FormGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Switch from '@material-ui/core/Switch';
import Checkbox from '@material-ui/core/Checkbox';
import Select from '@material-ui/core/Select';
import InputLabel from '@material-ui/core/InputLabel';
import FormControl from '@material-ui/core/FormControl';
import moment from 'moment';
import Downshift from "downshift";
import Autocomplete from "../entity/TestAutocomplete";
import CustomFileInput from "reactstrap/es/CustomFileInput";
import CircularProgress from '@material-ui/core/CircularProgress';
import {contactType, StateRegion, FillingStructureData} from '../../StaticData/Static';
import RoomIcon from '@material-ui/icons/Room';
import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';
import BusinessIcon from '@material-ui/icons/Business';
import MailIcon from '@material-ui/icons/Mail';
import Container from "@material-ui/core/Container";
import Link from "@material-ui/core/Link";
import {useHistory} from "react-router-dom";
import {entityDetail} from '../../crud/enitity.crud';
import {OktaUserContext} from '../../context/OktaUserContext';
const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
    },
    paper: {
        padding: theme.spacing(2),
        textAlign: 'center',
        color: theme.palette.text.secondary,
    },

    adjustment: {
        marginRight: '5px',
    },

    companyinfo: {
        listStyle: 'none',
        padding: '0px',
        minHeight: '100px'

    },
    listItem: {
        marginBottom: '5px'
    }
}));


const IndexProcessExceptionDetailedPage = (props) => {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const classes = useStyles();
    const history = useHistory();
    const [entitydetail, setEntitydetail] = React.useState();
    const [loading, setLoading] = React.useState(true);
    useEffect(()=>{
        fetchDetailedProfile();
    },[])


   const fetchDetailedProfile = async () => {
      const detailedView = await entityDetail(oktaprofile.organization, oktaprofile.email,props.match.params.id);
       setEntitydetail(detailedView.result)
       setLoading(false);
   }






    if(entitydetail){console.log(entitydetail)}

    return (

        <>
            {entitydetail ? <Title title={entitydetail.entity.entity_name}/> : <Title title={''}/> }

            <Grid container spacing={1}>
                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<RoomIcon className={classes.adjustment}/>} title="Index Process Exception Detail"/>
                        <PortletBody>
                        <div className="row">
                                <form className={classes.container} onSubmit={handleOnSubmit} noValidate
                                      autoComplete="off">
                                    <FormGroup row>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                error={inputName.error !== ' '}
                                                onChange={e => console.log(e)}
                                                id="fetched_entity_name"
                                                label="Fetched Entity Name"
                                                className={clsx(classes.textField, classes.dense)}
                                                margin="dense"
                                                helperText={fetched_entity_name.error}
                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <FormControlLabel
                                                disabled={loading}
                                                onChange={e => console.log(e)}
                                                value={'test'}
                                                control={<Checkbox color="primary"/>}
                                                label="Compliance Only"
                                                className={clsx(classes.textField, classes.checkbox)}
                                                labelPlacement="start"
                                            />
                                        </div>

                                        <div className={'col-md-12'}>
                                            <div className={clsx(classes.submitButton, 'custom-button-wrapper')}>
                                                {loading ? (
                                                        <div className={clsx(classes.loader)}>
                                                            <FacebookProgress/>
                                                        </div>)
                                                    : null}
                                                <input className={clsx('btn btn-primary', classes.restButton)}
                                                       type="submit" value="Reset"/>

                                                <input className={clsx('btn btn-primary', classes.restButton)}
                                                       type="submit" value="Create New Entity"/>

                                            </div>
                                        </div>
                                    </FormGroup>
                                </form>
                            </div>
                        </PortletBody>
                    </Portlet>
                </Grid>

            </Grid>

        </>
    )
}

export default withAuth(IndexProcessExceptionDetailedPage);

